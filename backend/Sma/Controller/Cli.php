<?php
namespace Sma\Controller;

use Osf\Controller\Cli as OsfControllerCli;
use Osf\Exception\ArchException;
use Osf\Test\Runner as OsfTest;
use Sma\Generator\SmaGenerator;
use Sma\Search\Indexer;
use Sma\Db\DbRegistry;
use Sma\Cache as SC;
use Sma\Container;
use Sma\Generator;
use Sma\Acl;
use Sma\Log;
use App\Recipient\Model\RecipientDbManager as RDM;
use Exception;
use DB, C;

/**
 * SMA specific command line tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage cli
 */
class Cli extends OsfControllerCli
{
    protected static $configFile = APPLICATION_PATH . '/App/Common/Config/application.php';
    protected static $databases = [
        ['adapter' => 'admin',
         'generatorParams' => [],
         'comment' => 'administration database'],
        ['adapter' => 'common',
         'generatorParams' => [],
         'comment' => 'application database'],
    ];
    protected static $generators = [];

    public static function run() {
        
        // Adaptateur pour les logs
        Log::setAdapter(new Log\CliAdapter());
        
        // Initialise l'environnement, la configuration, les adaptateurs BD
        Container::getApplication()->init()->bootstrap();
        
        // Lance les actions
        parent::run();
    }
    
//    /**
//     * Generate CSS stylesheet from less files
//     */
//    protected static function lessAction()
//    {
//        echo self::beginActionMessage('Common CSS generation');
//        $lessc = '/usr/bin/lessc';
//        if (!file_exists($lessc) || !is_executable($lessc)) {
//            echo self::endActionFail();
//            self::displayError('lessc executable not found, please install it');
//            return;
//        }
//        $file = APPLICATION_PATH . '/App/Common/Config/less/styles.less';
//        $cssFile = APP_PATH . '/htdocs/www/styles/styles.css';
//        $cmd = escapeshellcmd($lessc) . ' -x ' . escapeshellarg($file)
//                . ' > ' . escapeshellarg($cssFile)
//                . ' 2> /tmp/lessc-errors.log';
//        $retVal = null;
//        passthru($cmd, $retVal);
//        if ($retVal == 0) {
//            echo self::endActionOK();
//        } else {
//            echo self::endActionFail();
//            self::displayError('See /tmp/lessc-errors.log to view errors');
//        }
//    }

    // @task mettre en place les tests unitaires applicatifs

    /**
     * Application unit tests
     */
    protected static function testAction(...$args)
    {
        if (!isset($args[0])) {
            $args[0] = realpath(__DIR__ . '/../../');
        }
        $rootPath = self::getRootPath($args);
        self::display("Running tests in " . $rootPath);
        // set_include_path($rootPath . ':' . get_include_path());
        OsfTest::runDirectory($rootPath);
    }

    /**
     * Clean cache 
     */
    protected static function cleanAction()
    {
        $args = func_get_args();
        $arg = isset($args[0]) ? $args[0] : null;
        $args = ['acl', 'zend', 'cache', 'smacache', 'backups', 'notif', 'all'];
        try {
            if ($arg === 'acl' || $arg === 'all') {
                echo self::beginActionMessage('Cleaning ACLs');
                Container::getCache()->clean(Acl::REDIS_CACHE_KEY);
                echo self::endActionOK();
            }
            if ($arg === 'zend' || $arg === 'all') {
                echo self::beginActionMessage('Cleaning zend cache (translate)');
                foreach (Container::getCache()->getRedis()->keys('zfcache:*') as $key) {
                    Container::getCache()->getRedis()->del($key);
                }
                echo self::endActionOK();
            }
            if ($arg === 'smacache' || $arg === 'all') {
                echo self::beginActionMessage('Cleaning applications cache (sma)');
                foreach (Container::getCache()->getRedis()->keys(SC::NAMESPACE . '*') as $key) {
                    Container::getCache()->getRedis()->del($key);
                }
                echo self::endActionOK();
            }
            if ($arg === 'cache' || $arg === 'all') {
                echo self::beginActionMessage('Cleaning redis cache');
                Container::getCache()->cleanAll(); // namespace "osfcache"
                C::cleanAll(); // namespace "cache"
                echo self::endActionOK();
            }
            if ($arg === 'backups' || $arg === 'all') {
                echo self::beginActionMessage('Cleaning old backup dumps');
                self::cleanOldBackupFiles();
                echo self::endActionOK();
            }
            if ($arg === 'notif' || $arg === 'all') {
                echo self::beginActionMessage('Cleaning old notifications');
                DbRegistry::notificationClean();
                echo self::endActionOK();
            }
            if (!in_array($arg, $args)) {
                self::displayError('Specify a keyword (' . implode('|', $args) . ')');
            }
        } catch (Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }        
    }
    
    /**
     * Launch SMA application generators (static quick access classes)
     */
    protected static function appgenAction()
    {
        self::checkOnlyDevEnv();
        
        // Helpers
        echo self::beginActionMessage(APP_SNAM . ' application generators');
        try {
            (new Generator())->generateSmaHelpers();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // ACL
        echo self::beginActionMessage(APP_SNAM . ' acl -> acl.php');
        try {
            Generator::generateAcl();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // App configs
        echo self::beginActionMessage('Apps configurations -> apps.php');
        try {
            Generator::generateApp();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // MENU
        echo self::beginActionMessage('App menus -> menu.php');
        try {
            Generator::generateMenu();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // VERSION 
//        echo self::beginActionMessage(APP_SNAM . ' version builder -> Version.php');
//        try {
//            Generator::generateVersion();
//            echo self::endActionOK();
//        } catch (\Exception $e) {
//            echo self::endActionFail();
//            self::displayError($e->getMessage());
//        }
//
        // GENERAL CONFIGURATION
        // Conditions sur general.yml / mise en cache pour l'instant
//        echo self::beginActionMessage('General config YAML -> general.php');
//        try {
//            Generator::generateGeneralConfig();
//            echo self::endActionOK();
//        } catch (\Exception $e) {
//            echo self::endActionFail();
//            self::displayError($e->getMessage());
//        }
        
        // VIEW HELPERS
        echo self::beginActionMessage('SMA View Helpers');
        try {
            (new SmaGenerator())->generateAll();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
    }
    
    /**
     * Fix hashes, search engine content, etc.
     */
    protected static function fixesAction()
    {
        // HASHS
        echo self::beginActionMessage('Company hashes');
        try {
            DB::getCompanyTable()->fixAllHashes();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // Document recipients
        echo self::beginActionMessage('Document recipients');
        try {
            DB::getDocumentTable()->fixDocumentRecipients();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // Document subjects
        echo self::beginActionMessage('Document subjects');
        try {
            DB::getDocumentTable()->fixDocumentSubjects();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
        
        // Document subjects
        echo self::beginActionMessage('Contacts without bean');
        try {
            RDM::fixContactBeans();
            echo self::endActionOK();
        } catch (\Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
    }

    /**
     * Execute deferred actions (log register, cache generation...)
     */
    protected static function tickAction()
    {
        self::registerDeferredClass('\Sma\Controller\Cli\DeferredLogProcessing');
        self::registerDeferredClass('\Sma\Controller\Cli\DeferredMailProcessing');
//        self::registerDeferredClass('\Sma\Controller\Cli\DeferredBasketProcessing');
        return parent::tickAction();
    }

    /**
     * Global search engine indexation
     */
    protected static function indexAction()
    {
        $args = func_get_args();
        if (!isset($args[0]) || (!is_numeric($args[0]) && $args[0] !== 'all')) {
            self::displayError('Specify an account id (<int>|all)');
        }
        try {
            if ($args[0] === 'all') {
                echo self::beginActionMessage('Global index update');
                foreach (DB::getAccountTable()->select() as $account) {
                    Indexer::indexAll($account->getId());
                }
            } else {
                $idAccount = (int) $args[0];
                echo self::beginActionMessage('Index update for account #' . $idAccount);
                Indexer::indexAll($idAccount);
            }
            echo self::endActionOK();
        } catch (Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
    }

    /**
     * Account backup
     */
    protected static function backupAction()
    {
        $args = func_get_args();
        if (!isset($args[0]) || (!is_numeric($args[0]) && $args[0] !== 'all')) {
            self::displayError('Specify an account id (<int>|all)');
        }
        if ($args[0] === 'all') {
            $backupDir = DbRegistry::getBackupDir() . '/generals/' . date('Ymd-His');
            mkdir($backupDir);
            foreach (DB::getAccountTable()->select() as $account) {
                self::backupAccount($account->getId(), false, $backupDir);
            }
            self::display('Backup files in: ' . $backupDir);
        } else {
            self::backupAccount((int) $args[0]);
        }
    }

    protected static function backupAccount(int $idAccount, $displayGeneratedFile = true, $backupDir = null)
    {
        echo self::beginActionMessage('Backup of account #' . $idAccount);
        try {
            $file = DbRegistry::backupAccount($idAccount, $backupDir);
            echo self::endActionOK();
            echo $displayGeneratedFile ? self::display('Backup file is: ' . $file) : '';
        } catch (Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
            return false;
        }
        return $file;
    }
    
    /**
     * Account recovery
     */
    protected static function recoverAction()
    {
        $args = func_get_args();
        if (!isset($args[0]) || !is_string($args[0]) || !file_exists($args[0])) {
            self::displayError('Specify a valid backup file');
            return;
        }
        try {
            echo self::beginActionMessage("Account recovery");
            $idAccount = DbRegistry::recoverAccount($args[0]);
            echo self::endActionOK();
            echo self::beginActionMessage("Search engine index update for #" . $idAccount);            
            Indexer::indexAll($idAccount);
            echo self::endActionOK();
        } catch (Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
    }
    
    /**
     * Delete an account from database
     */
    protected static function deleteAction()
    {
        $args = func_get_args();
        if (!isset($args[0]) || !is_numeric($args[0])) {
            self::displayError('Specify a valid account id');
        }
        $idAccount = (int) $args[0];
        
        // Sauvegarde préalable
        if (!self::backupAccount($idAccount)) {
            self::displayError("Unable to backup before delete. Abort.");
            return;
        }
        
        // Suppression
        try {
            echo self::beginActionMessage("Deletion of account #" . $idAccount);            
            DbRegistry::truncateAccount($idAccount);
            echo self::endActionOK();
        } catch (Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
    }
    
    
    /**
     * List accounts (arg 1 = filter)
     */
    protected static function listAction()
    {
        $args = func_get_args();
        $filter = isset($args[0]) ? $args[0] : null;
        
        // Listage
        try {
            echo DB::getAccountTable()->getList($filter);
        } catch (Exception $e) {
            self::displayError($e->getMessage());
        }
    }
    
    /**
     * Build form & survey stats (arg 1 = form class)
     */
    protected static function statsAction()
    {
        $args = func_get_args();
        $formClass = isset($args[0]) ? $args[0] : null;
        
        // Listage
        try {
            $msg = $formClass === null ? "Building all form stats" : "Building [" . $formClass . '] form stats';
            echo self::beginActionMessage($msg);            
            $results = DB::getFormStatsTable()->buildStats($formClass);
            if ($results) {
                echo self::endActionOK();
                self::display($results . ' form stats rows builded');
            } else {
                echo self::endActionSkip();
                self::displayError('No form stat found');
            }
        } catch (Exception $e) {
            echo self::endActionFail();
            self::displayError($e->getMessage());
        }
    }
    
    // FONCTIONS UTILITAIRES
    
    /**
     * Supprime les vieux fichiers de backup (rotate)
     */
    protected static function cleanOldBackupFiles(): void
    {
        $backupRootDir = DbRegistry::getBackupDir();
        
        // Nettoyage des backups dans les répertoires de comptes
        $accountDirs = glob($backupRootDir . '/accounts/*');
        foreach ($accountDirs as $dir) {
            if (!is_numeric(basename($dir))) {
                throw new ArchException($dir . ' account dir is not an id ?');
            }
            self::cleanFiles($dir . '/20*');
        }
        
        // Nettoyage des backups dans le répertoire général
        self::cleanFiles($backupRootDir . '/generals/20*');
        
        // Nettoyage des backups de la base de données
        self::cleanFiles($backupRootDir . '/database/20*');
        
        // Nettoyage des liens symboliques cassés 
        $rootDir = escapeshellarg($backupRootDir);
        $cmd = 'find ' . $rootDir . ' -xtype l -exec rm {} \;';
        passthru($cmd);
    }
    
    /**
     * Parcours et clean d'un glob pattern de files ou dirs
     * @staticvar type $whiteDates
     * @param string $filesPattern
     * @return void
     * @throws ArchException
     */
    protected static function cleanFiles(string $filesPattern): void
    {
        static $whiteDates = null;
        
        if ($whiteDates === null) {
            $today = time();
            $whiteDates = [];
            for ($i = 0; $i < 7; $i++) {
                $whiteDates[date('Ymd', $today - ($i * 3600 * 24))] = true;
            }
            for ($i = 0; $i < 50; $i++) {
                $whiteDates[date('Ym', $today - ($i * 3600 * 24 * 15)) . '01'] = true;
            }
        }
        
        $dirOrFiles = glob($filesPattern);
        foreach ($dirOrFiles as $dirOrFile) {
            $date = substr(basename($dirOrFile), 0, 8);
            if (!preg_match('/^20[0-9]{6}$/', $date)) {
                throw new ArchException($date . ' dir or file syntax is not correct');
            }
            if (array_key_exists($date, $whiteDates)) {
                continue;
            }
            if (is_dir($dirOrFile)) {
                foreach (glob($dirOrFile . '/*') as $file) {
                    unlink($file);
                }
                rmdir($dirOrFile);
            } else {
                unlink($dirOrFile);
            }
        }
    }
    
    /**
     * @return string
     */
    protected static function getCurrentClass(): string
    {
        return __CLASS__;
    }
}
