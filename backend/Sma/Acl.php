<?php
namespace Sma;

use Zend\Permissions\Acl\Role\GenericRole as Role;
use Osf\Application\Acl as OsfAcl;
use Osf\Controller\Cli;
use Osf\Stream\Yaml;
use Osf\Exception\ArchException as AE;
use Sma\Session\Identity as I;

/**
 * High level ACL for webapps
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage acl
 */
class Acl extends OsfAcl
{
    const REDIS_CACHE_KEY = 'SMA_ACL';
    
    const ROLE_GUEST = 'GUEST';      // Clients finaux
    const ROLE_ACCOUNTANT = 'ACCNT'; // Expert comptable
    const SMA_ROLES = [
        self::ROLE_GUEST => self::ROLE_PUBLIC,
        self::ROLE_ACCOUNTANT => self::ROLE_PUBLIC
    ];
    
    /**
     * @return $this
     */
    protected function buildCommonRoles()
    {
        parent::buildCommonRoles();
        foreach (self::SMA_ROLES as $role => $parent) {
            $this->addRole($role, $parent);
        }
        return $this;
    }
    
    /**
     * Calculate and get current role
     * @staticvar string $role
     * @return string
     */
    public function getCurrentRole(): string
    {
        $this->buildAcl();
        if (I::getAclRole()) {
            $role = I::getAclRole();
        } else if (I::isLogged()) {
            $email = Session\Identity::get('email');
            if (!$this->hasRole($email)) {
                $parent = $this->isAdmin($email) ? self::ROLE_ADMIN : self::ROLE_LOGGED;
                $this->addRole($email, $parent);
            }
            $role = $email;
        } else {
            $role = self::ROLE_NOT_LOGGED;
        }
        return $role;
    }
    
    /**
     * Is current user (default) is admin ?
     * @param string $email
     * @return type
     */
    public function isAdmin(?string $email = null): bool
    {
        if ($email === null && I::isLogged()) {
            $email = I::get('email');
        }
        return parent::isAdmin($email);
    }
    
    /**
     * Is current user (defaut) allowed to access request params ? (role or email)
     * @param string $controller
     * @param string $action
     * @param string $role
     * @param string $email
     * @return bool
     */
    public function isAllowedParams(?string $controller = null, ?string $action = null, ?string $role = null, ?string $email = null): bool
    {
//        if ($role && $email) {
//            throw new ArchException('Do not specify $role and $email');
//        }
        if ($role === self::ROLE_ADMIN || (!Cli::isCli() && $this->isAdmin())) {
            return true;
        }
        if ($email && $this->isAdmin($email)) {
            return true;
        }
        $role = $role ?? $this->getCurrentRole();
        return parent::isAllowedParams($controller, $action, $role);
    }
    
    /**
     * Retourne le rôle correspondant à l'email spécifié
     * @param string $email
     * @return string
     */
    public function getRoleFromEmail(?string $email): string
    {
        if (!$email) {
            return self::ROLE_NOT_LOGGED;
        }
        if ($this->hasRole($email)) {
            return $email;
        }
        return $this->isAdmin($email) ? self::ROLE_ADMIN : self::ROLE_LOGGED;
    }
    
    // Zend Acl heritages
    
    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend\Permissions\Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  Role\RoleInterface|string            $role
     * @param  Resource\ResourceInterface|string    $resource
     * @param  string                               $privilege
     * @return bool
     */
    public function isAllowed($role = null, $resource = null, $privilege = null)
    {
        $role = $role ?: $this->getCurrentRole();
        return parent::isAllowed($role, $resource, $privilege);
    }
    
    /**
     * Build acl.php from applications acl.yml
     * @return array
     * @throws AE
     */
    public static function buildAclFromApps(): array
    {
        $apps = glob(APPLICATION_PATH . '/App/*');
        $acl = ['admin' => []];
        foreach ($apps as $dir) {
            $app = strtolower(basename($dir));
            self::appendAcl($acl, $dir . '/Config/acl.yml', $dir . '/Config/.acl.yml', $app);
        }
        return $acl;
    }
    
    /**
     * @param array $acl
     * @param string $file
     * @return void
     * @throws AE
     */
    protected static function appendAcl(array &$acl, string $file, string $localFile, string $app): void
    {
        if (file_exists($file)) {
            $appAcl = Yaml::parseFile($file);
            if (file_exists($localFile)) {
                $localAcl = Yaml::parseFile($localFile);
                $appAcl = array_merge_recursive($appAcl, $localAcl);
            }
            if (!isset($appAcl['controller'])) { throw new AE('controller section required in ' . $file); }
            if (!isset($appAcl['action']))     { throw new AE('action section required in ' . $file); }
            $acl['controller'][$app] = $appAcl['controller'];
            $acl['action'][$app] = $appAcl['action'];
            if ($app === 'common' && isset($appAcl['admin'])) {
                $acl['admin'] = $appAcl['admin'];
            }
        }
    }
    
    /**
     * Returns true if and only if $role inherits from $inherit
     *
     * Both parameters may be either a Role or a Role identifier. If
     * $onlyParents is true, then $role must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $role
     * inherits from $inherit through its ancestor Roles.
     *
     * @param  Role\RoleInterface|string    $role
     * @param  Role\RoleInterface|string    $inherit ! behind = not inherits
     * @param  bool                      $onlyParents
     * @return bool
     */
    public function inheritsRole($role, $inherit, $onlyParents = false)
    {
        $role = $role ?: $this->getCurrentRole();
        return parent::inheritsRole($role, $inherit, $onlyParents);
    }
}
