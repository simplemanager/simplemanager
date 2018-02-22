<?php
namespace Sma;

use Sma\Controller\Cli;
use Sma\Install\Config as Config;
use Zend\Validator\EmailAddress;

/**
 * SimpleManager installation process
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since SMA-0.1 - 2018
 * @package sma
 * @subpackage install
 */
class Install extends Cli
{
    const ACL_YML_FILE = APP_PATH . '/etc/acl.yml';
    const APP_PHP_FILE = APP_PATH . '/etc/application.php';
    
    const REQUIRED_EXT = [
        'date',
        'dom',
        'fileinfo',
        'filter',
        'gd',
        'gettext',
        'hash',
        'iconv',
        'imagick',
        'intl',
        'json',
        'libxml',
        'mbstring',
        'mysqli',
        'mysqlnd',
        'openssl',
        'pcre',
        'redis',
        'Reflection',
        'session',
        'SimpleXML',
        'SPL',
        'xml',
        'zip',
        'zlib'
    ];
    const REQUIRED_PHP_VERSION = '7.1.3';
    
    /**
     * @var Config
     */
    protected $config;
    
    /**
     * @var \mysqli
     */
    protected $mysqli;
    
    protected $logFile;
    
    public function __construct(?Config $config = null)
    {
        defined('SMA_INSTALL') || define('SMA_INSTALL', true);
        $this->config = $config ?? new Config();
        $this->logFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . date('Ymd-His-') . 'sma-install.log';
    }
    
    public function install()
    {
        $this->welcome();
        $this->checkEnv();
        $this->checkArgs();
        $this->databases();
        $this->configuration();
        $this->generate();
        $this->clean();
        // $this->test();
        $this->by(0);
    }
    
    protected function welcome()
    {
        echo "\n";
        echo "+------------------------------------------------------------------------------+\n";
        echo "|             " . self::green() . "WELCOME TO THE SIMPLEMANAGER INSTALLATION PROCEDURE" . self::resetColor() . "              |\n";
        echo "+------------------------------------------------------------------------------+\n\n";
    }
    
    protected function checkEnv()
    {
        echo self::beginActionMessage('Checking the prerequisites');
        $errors = [];
        
        // PHP version
        if (version_compare(PHP_VERSION, self::REQUIRED_PHP_VERSION) < 0) {
            $errors[] = 'PHP version must be >=' . self::REQUIRED_PHP_VERSION;
        }
        
        // PHP extensions
        foreach (self::REQUIRED_EXT as $ext) {
            if (!extension_loaded($ext)) {
                $errors[] = 'PHP extension [' . $ext . '] is required';
            }
        }
        
        // Already installed ?
        if (file_exists(self::ACL_YML_FILE)) {
            echo self::endActionSkip();
            self::displayError('This application is already installed.', false, false);
            self::display('Remove ' . self::ACL_YML_FILE . ' to reinstall.');
            echo "\n";
            exit(0);
        }
        
        // File system
        if (!is_writable(APP_PATH)) {
            $errors[] = APP_PATH . ' must be writable for the installation process';
        }
        
        if ($errors) {
            echo self::endActionFail();
            foreach ($errors as $str) {
                self::displayError($str, false, false);
            }
            $this->end();
        }
        echo self::endActionOK();
    }
    
    protected function checkArgs(): void
    {
        echo self::beginActionMessage('Checking installation data');
        
        // Args
        if (!$this->config->getAdminemail()) {
            echo self::endActionSkip();
            self::displayError('admin e-mail required, help yourself with the following options:', false, false);
            echo $this->config->getOptUsage(); 
            $this->end();
        }
        if (!(new EmailAddress())->isValid($this->config->getAdminemail())) {
            echo self::endActionFail();
            self::displayError('bad e-mail address syntax [' . $this->config->getAdminemail() . ']');
        }
        $pattern = '/^[a-zA-Z0-9_-]{1,30}$/';
        if (!preg_match($pattern, $this->config->getMaindbname())) {
            echo self::endActionFail();
            self::displayError('bad main db name syntax, must match ' . $pattern);
        }
        if (!preg_match($pattern, $this->config->getCommondbname())) {
            echo self::endActionFail();
            self::displayError('bad common db name syntax, must match ' . $pattern);
        }
        
        // Redis
        try {
            $redis = new \Redis();
            $redis->connect($this->config->getRedishostname());
            $redis->auth($this->config->getRedisauth());
            $redis->get('test');
        } catch (\RedisException $e) {
            echo self::endActionFail();
            self::displayError('redis connexion failed [' . $e->getMessage() . ']');
        }
        
        // Mysql
        $this->mysqli = mysqli_init();
        $connect = @$this->mysqli->real_connect($this->config->getSgdbhostname(), $this->config->getDbuser(), $this->config->getDbpass());
        if (!$connect) {
            echo self::endActionFail();
            self::displayError('mysql connexion failed: ' . $this->mysqli->error);
        }
        $this->mysqli->close();
        $this->exec('mysql --version');
        echo self::endActionOK();
    }
    
    protected function databases(): void
    {
        echo self::beginActionMessage('Databases installation');
        echo $this->mysqlInstall() ? self::endActionOK() : self::endActionSkip();
    }
    
    protected function configuration(): void
    {
        echo self::beginActionMessage('Var directory building');
        $varDir = APP_PATH . '/var';
        if (is_dir($varDir . '/backup') && is_dir($varDir . '/log') && is_dir($varDir . '/cache')) {
            echo self::endActionSkip();
        } else {
            $ok = true;
            $ok = mkdir($varDir . '/backup', 0755, true) && $ok;
            $ok = mkdir($varDir . '/log', 0755, true) && $ok;
            $ok = mkdir($varDir . '/cache', 0755, true) && $ok;
            echo $ok ? self::endActionOK() : self::endActionFail();
            if (!$ok) {
                self::displayError('Unable to create var directories');
            }
        }
        
        echo self::beginActionMessage('Application general configuration');
        if (file_exists(self::APP_PHP_FILE)) {
            copy(self::APP_PHP_FILE, self::APP_PHP_FILE . '.bak');
        }
        $conf = "<?php\n// Local SMA config file\n\nreturn [
    'db' => [
        'admin' => [
            'database' => '" . $this->config->getMaindbname() . "',
            'hostname' => '" . $this->config->getSgdbhostname() . "',
            'username' => '" . $this->config->getDbuser() . "',
            'password' => '" . $this->config->getDbpass() . "',
        ],
        'common' => [
            'database' => '" . $this->config->getCommondbname() . "',
            'hostname' => '" . $this->config->getSgdbhostname() . "',
            'username' => '" . $this->config->getDbuser() . "',
            'password' => '" . $this->config->getDbpass() . "',
        ]
    ],
    'redis' => [
        'host' => '" . $this->config->getRedishostname() . "',
        'auth' => '" . $this->config->getRedisauth() . "',
    ],
    'mail' => [
        'debug' => ['mail' => '" . $this->config->getAdminemail() . "', 'name' => 'SimpleManager tester'],
        'admin' => ['mail' => '" . $this->config->getAdminemail() . "', 'name' => sprintf('Contact %s (dev)', APP_NAME)]
    ]
];\n";
        file_put_contents(self::APP_PHP_FILE, $conf);
        echo self::endActionOK();
            
        echo self::beginActionMessage('Application acl configuration');
        $conf = "# ACL local configuration\n\nadmin:\n  - " . $this->config->getAdminemail() . "\n";
        file_put_contents(self::ACL_YML_FILE, $conf);
        echo self::endActionOK();
    }
    
    protected function generate(): void
    {
        self::appgenAction();
    }
    
    protected function clean(): void
    {
        self::cleanAction('acl');
        self::cleanAction('zend');
        self::cleanAction('cache');
        self::cleanAction('smacache');
        self::cleanAction('backups');
    }

    protected function test(): void
    {
        self::testAction();
    }
    
    protected function mysqlInstall(): bool
    {
        $cmdPrefix = 'mysql'
            . ' --host=' . escapeshellarg($this->config->getSgdbhostname())
            . ' --user=' . escapeshellarg($this->config->getDbuser())
            . ' --password=' .escapeshellarg($this->config->getDbpass());

        $tmpAdmFile = sys_get_temp_dir() . '/sma_admin.sql';
        $tmpComFile = sys_get_temp_dir() . '/sma_common.sql';
        $tmpDatFile = sys_get_temp_dir() . '/data.sql';
        
        $from = [
            'sma_admin',
            'sma_common'
        ];
        $to = [
            $this->config->getMaindbname(),
            $this->config->getCommondbname()
        ];
        
        file_put_contents($tmpAdmFile, str_replace($from, $to, file_get_contents(__DIR__ . '/Install/files/sma_admin.sql')));
        file_put_contents($tmpComFile, str_replace($from, $to, file_get_contents(__DIR__ . '/Install/files/sma_common.sql')));
        
        $data = str_replace('{{EMAIL}}', $this->config->getAdminemail(), file_get_contents(__DIR__ . '/Install/files/data.sql'));
        file_put_contents($tmpDatFile, str_replace($from, $to, $data));
        
        $this->exec($cmdPrefix . ' < ' . escapeshellarg($tmpAdmFile));
        $this->exec($cmdPrefix . ' < ' . escapeshellarg($tmpComFile));
        $this->exec($cmdPrefix . ' < ' . escapeshellarg($tmpDatFile));
        
        unlink($tmpAdmFile);
        unlink($tmpComFile);
        unlink($tmpDatFile);
        
        return true;
    }
    
    protected function exec($cmd): void
    {
        $this->log($cmd);
        exec($cmd . ' 2>&1', $result, $return);
        $this->log(implode("\n", $result));
        if ($return !== 0) {
            echo self::endActionFail();
            self::displayError('a command returns an error ' . $return . ', details: ' . $this->logFile);
        }
    }
    
    protected function log($data): void
    {
        file_put_contents($this->logFile, $data . "\n", FILE_APPEND);
    }
    
    protected function by(int $returnValue = 1): void
    {
        echo "\n";
        echo "  " . self::green() . "SimpleManager is installed ;)" . self::resetColor() . "\n\n";
        echo "  -> Go to http://localhost:8080\n"; 
        echo "  -> Login: " . self::yellow() . $this->config->getAdminemail() . self::resetColor() . "\n";
        echo "  -> Password: " . self::yellow() . "password42" . self::resetColor() . "\n";
        echo "  -> Complete your profile, " . self::red() . "change the password" . self::resetColor() . " and have fun!\n";
        echo "\n";
        exit($returnValue);
    }
}
