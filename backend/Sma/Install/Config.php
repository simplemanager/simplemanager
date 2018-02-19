<?php
namespace Sma\Install;

use Osf\Bean\AbstractBean;
use GetOpt\GetOpt;
use GetOpt\Option;

/**
 * Installation configuration
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-3.0.0 - 2018
 * @package sma
 * @subpackage install
 */
class Config extends AbstractBean
{
    const OPTIONS = [
        'adminemail',
        'adminname',
        'dbuser',
        'dbpass',
        'maindbname',
        'commondbname',
        'sgdbhostname',
        'redishostname',
        'redisauth'
    ];
    
    protected $adminemail    = null;
    protected $adminname     = 'admin';
    protected $dbuser        = 'root';
    protected $dbpass        = '';
    protected $maindbname    = 'sma_admin';
    protected $commondbname  = 'sma_common';
    protected $sgdbhostname  = 'localhost';
    protected $redishostname = 'localhost';
    protected $redisauth     = '';
    
    /**
     * @var GetOpt
     */
    protected $opt;
    
    public function __construct()
    {
        $values = [];
        foreach (self::OPTIONS as $key) {
            $values[$key] = (string) ($this->getOpt()->getOption($key) ?? $this->$key);
        }
        $this->populate($values);
    }
    
    /**
     * @return GetOpt
     */
    protected function getOpt(): GetOpt
    {
        $getOpt = new GetOpt([
            Option::create('e', 'adminemail',     GetOpt::REQUIRED_ARGUMENT)->setDescription('Administrator e-mail'),
//            Option::create(null, 'adminname',     GetOpt::OPTIONAL_ARGUMENT)->setDescription('Administrator name (default: ' . $this->adminname . ')'),
            Option::create(null, 'sgdbhostname',  GetOpt::OPTIONAL_ARGUMENT)->setDescription('Mysql server hostname (default: ' . $this->sgdbhostname . ')'),
            Option::create(null, 'dbuser',        GetOpt::OPTIONAL_ARGUMENT)->setDescription('Databases user (default: ' . $this->dbuser . ')'),
            Option::create(null, 'dbpass',        GetOpt::OPTIONAL_ARGUMENT)->setDescription('Databases password (default: ' . $this->dbpass . ')'),
            Option::create(null, 'maindbname',    GetOpt::OPTIONAL_ARGUMENT)->setDescription('Main database name (default: ' . $this->maindbname . ')'),
            Option::create(null, 'commondbname',  GetOpt::OPTIONAL_ARGUMENT)->setDescription('Common database name (default: ' . $this->commondbname . ')'),
            Option::create(null, 'redishostname', GetOpt::OPTIONAL_ARGUMENT)->setDescription('Redis hostname (default: ' . $this->redishostname . ')'),
            Option::create(null, 'redisauth',     GetOpt::OPTIONAL_ARGUMENT)->setDescription('Redis auth (default: ' . $this->redisauth . ')'),
        ]);
        $getOpt->process();
        return $getOpt;
    }
    
    public function getOptUsage(): string
    {
        return preg_replace('/sma /', 'sma install ', $this->getOpt()->getHelpText(), 1);
    }
    
    /**
     * @param string $adminname
     * @return $this
     */
    public function setAdminname(string $adminname)
    {
        $this->adminname = $adminname;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdminname(): string
    {
        return $this->adminname;
    }
    
    /**
     * @param string $adminemail
     * @return $this
     */
    public function setAdminemail(string $adminemail)
    {
        $this->adminemail = $adminemail;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdminemail(): string
    {
        return $this->adminemail;
    }
    
    /**
     * @param string $dbuser
     * @return $this
     */
    public function setDbuser(string $dbuser)
    {
        $this->dbuser = $dbuser;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbuser(): string
    {
        return $this->dbuser;
    }
    
    /**
     * @param string $dbpass
     * @return $this
     */
    public function setDbpass(string $dbpass)
    {
        $this->dbpass = $dbpass;
        return $this;
    }

    /**
     * @return string
     */
    public function getDbpass(): string
    {
        return $this->dbpass;
    }
    
    /**
     * @param string $maindbname
     * @return $this
     */
    public function setMaindbname(string $maindbname)
    {
        $this->maindbname = $maindbname;
        return $this;
    }

    /**
     * @return string
     */
    public function getMaindbname(): string
    {
        return $this->maindbname;
    }
    
    /**
     * @param string $commondbname
     * @return $this
     */
    public function setCommondbname(string $commondbname)
    {
        $this->commondbname = $commondbname;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommondbname(): string
    {
        return $this->commondbname;
    }
    
    /**
     * @param string $sgdbhostname
     * @return $this
     */
    public function setSgdbhostname(string $sgdbhostname)
    {
        $this->sgdbhostname = $sgdbhostname;
        return $this;
    }

    /**
     * @return string
     */
    public function getSgdbhostname(): string
    {
        return $this->sgdbhostname;
    }
    
    /**
     * @param string $redishostname
     * @return $this
     */
    public function setRedishostname(string $redishostname)
    {
        $this->redishostname = $redishostname;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedishostname(): string
    {
        return $this->redishostname;
    }
    
    /**
     * @param string $redisauth
     * @return $this
     */
    public function setRedisauth(string $redisauth)
    {
        $this->redisauth = $redisauth;
        return $this;
    }

    /**
     * @return string
     */
    public function getRedisauth(): string
    {
        return $this->redisauth;
    }
}
