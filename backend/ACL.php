<?php

use Sma\Container;
use Osf\Container\AbstractStaticContainer;

/**
 * Cache quick access
 *
 * This class is generated, do not edit it
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class ACL extends AbstractStaticContainer
{

    /**
     * Calculate and get current role
     * @staticvar string $role
     * @return string
     */
    public static function getCurrentRole()
    {
        return Container::getAcl()->getCurrentRole();
    }

    /**
     * Is current user (default) is admin ?
     * @param string $email
     * @return type
     */
    public static function isAdmin(string $email = null)
    {
        return Container::getAcl()->isAdmin($email);
    }

    /**
     * Is current user (defaut) allowed to access request params ? (role or email)
     * @param string $controller
     * @param string $action
     * @param string $role
     * @param string $email
     * @return bool
     */
    public static function isAllowedParams(string $controller = null, string $action = null, string $role = null, string $email = null)
    {
        return Container::getAcl()->isAllowedParams($controller, $action, $role, $email);
    }

    /**
     * Retourne le rôle correspondant à l'email spécifié
     * @param string $email
     * @return string
     */
    public static function getRoleFromEmail(string $email)
    {
        return Container::getAcl()->getRoleFromEmail($email);
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *      *
     * The $role and $resource parameters may be references to, or the string
     * identifiers for,
     * an existing Resource and Role combination.
     *      *
     * If either $role or $resource is null, then the query applies to all Roles or all
     * Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend\Permissions\Acl creates a
     * "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *      *
     * If a $privilege is not provided, then this method returns false if and only if
     * the
     * Role is denied access to at least one privilege upon the Resource. In other
     * words, this
     * method returns true if and only if the Role is allowed all privileges on the
     * Resource.
     *      *
     * This method checks Role inheritance using a depth-first traversal of the Role
     * registry.
     * The highest priority parent (i.e., the parent most recently added) is checked
     * first,
     * and its respective parents are checked similarly before the lower-priority
     * parents of
     * the Role are checked.
     *      *
     * @param  Role\RoleInterface|string            $role
     * @param  Resource\ResourceInterface|string    $resource
     * @param  string                               $privilege
     * @return bool
     */
    public static function isAllowed($role = null, $resource = null, $privilege = null)
    {
        return Container::getAcl()->isAllowed($role, $resource, $privilege);
    }

    /**
     * Build acl if needed
     * @return \Sma\Acl
     */
    public static function buildAcl()
    {
        return Container::getAcl()->buildAcl();
    }

    /**
     * Construit le nom de la ressource à partir du contrôleur et de l'action
     * @param string|null $controller
     * @param string|null $action
     * @return string|null
     */
    public static function buildResource(string $controller = null, string $action = null)
    {
        return Container::getAcl()->buildResource($controller, $action);
    }

    /**
     * Does the resource exists?
     * @return bool
     */
    public static function hasResourceParams($controller, $action = null)
    {
        return Container::getAcl()->hasResourceParams($controller, $action);
    }

    /**
     * Returns true if and only if the Role exists in the registry
     *      *
     * The $role parameter can either be a Role or a Role identifier.
     *      *
     * @param  Role\RoleInterface|string $role
     * @return bool
     */
    public static function hasRole($role)
    {
        return Container::getAcl()->hasRole($role);
    }

    /**
     * Returns true if and only if the Resource exists in the ACL
     *      *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *      *
     * @param  Resource\ResourceInterface|string $resource
     * @return bool
     */
    public static function hasResource($resource)
    {
        return Container::getAcl()->hasResource($resource);
    }

}