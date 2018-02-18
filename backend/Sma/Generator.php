<?php
namespace Sma;

use Osf\Generator\AbstractGenerator;
use Osf\Exception\ArchException;
use Osf\Controller\Router;
use Osf\Stream\Text as T;
use Osf\Application\OsfApplication as Application;
use Osf\Stream\Yaml;
use Sma\Acl;

/**
 * SMA generators
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage generator
 */
class Generator extends AbstractGenerator
{
    public function generateSmaHelpers()
    {
        // Layout
        $classes    = ['\Sma\Layout\Admin', '\Osf\Device\MobileDetect'];
        $containers = ['Container::getJsonRequest()', 'Container::getDevice()'];
        $uses       = ['App\Common\Container', 'Sma\Layout\AbstractLayoutContainer'];
        $namespace  = '';
        $className  = 'L';
        $fileName   = __DIR__ . '/../' . $className . '.php';
        $comment    = 'Json layout quick access + Mobile detect';
        $static     = false;
        $blackList  = [];
        $parent     = 'AbstractLayoutContainer';
        $blackList  = ['getScriptVersion', 'setHttpHeaders', 'getHttpHeaders',
                       'getHttpHeader', 'getMobileHeaders', 'getUaHttpHeaders',
                       'setCfHeaders', 'getCfHeaders', 'setUserAgent',
                       'getUserAgent', 'setDetectionType', 'getMatchingRegex',
                       'getMatchesArray', 'getPhoneDevices', 'getTabletDevices',
                       'getUserAgents', 'getBrowsers', 'getUtilities',
                       'getMobileDetectionRules', 'getMobileDetectionRulesExtended',
                       'getRules', 'getOperatingSystems', 'checkHttpHeadersForMobile',
                       'is', 'match', 'getProperties', 'prepareVersionNo',
                       'version', 'mobileGrade'];
        $this->generateStaticClass($classes, $containers, $uses, $namespace, 
                                   $className, $fileName, $comment, $static,
                                   $blackList, $parent);
        
        // Database models
        $classes    = ['\Sma\Db\DbContainer', '\Osf\Container\ZendContainer'];
        $containers = ['DbContainer', 'Zend'];
        $uses       = ['Sma\Db\DbContainer', 'Osf\Container\Zend'];
        $namespace  = '';
        $className  = 'DB';
        $fileName   = __DIR__ . '/../' . $className . '.php';
        $comment    = 'Database models quick access';
        $static     = true;
        $blackList  = ['buildObject', 'getInstances', 'setMockNamespace', 
                       'getAuth', 'getTranslate'];
        $this->generateStaticClass($classes, $containers, $uses, $namespace, 
                                   $className, $fileName, $comment, $static,
                                   $blackList);
        
        // Cache tool
        $classes    = ['\Sma\Cache'];
        $containers = ['Container::getCacheSma(\'cache\')'];
        $uses       = ['Sma\Container'];
        $namespace  = '';
        $className  = 'C';
        $fileName   = __DIR__ . '/../' . $className . '.php';
        $comment    = 'Cache quick access';
        $static     = false;
        $blackList  = [];
        $this->generateStaticClass($classes, $containers, $uses, $namespace, 
                                   $className, $fileName, $comment, $static,
                                   $blackList);
        
        // ACL
        $classes    = ['\Sma\Acl'];
        $containers = ['Container::getAcl()'];
        $uses       = ['Sma\Container'];
        $namespace  = '';
        $className  = 'ACL';
        $fileName   = __DIR__ . '/../' . $className . '.php';
        $comment    = 'Cache quick access';
        $static     = false;
        $blackList  = ['setRule', 'removeDeny', 'removeAllow', 'deny', 'allow',
                       'removeResourceAll', 'removeResource', 'addResource',
                       'removeRoleAll', 'removeRole', 'addRole', 'getRoles',
                       'getResources', 'inheritsRole', 'inheritsResource', 
                       'getRole', 'getResource'];
        $this->generateStaticClass($classes, $containers, $uses, $namespace, 
                                   $className, $fileName, $comment, $static,
                                   $blackList);
    }
    
    public static function generateAcl()
    {
        $acl = Acl::buildAclFromApps();
        $aclPhpFile = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Generated/acl.php';
        $content = "<?php \n\n"
                 . "// Global ACL generated file - dont edit\n"
                 . "// Generate with sma.php appgen\n\nreturn " 
                 . var_export($acl, true) . ";\n";
        file_put_contents($aclPhpFile, $content);
    }
    
    public static function generateApp()
    {
        $apps = glob(APPLICATION_PATH . '/App/*');
        $array = ['indexes' => [], 'app' => []];
        foreach ($apps as $dir) {
            $file = $dir . '/Config/app.yml';
            $app = strtolower(basename($dir));
            if (file_exists($file)) {
                $appCfg = Yaml::parseFile($file);
                if (!isset($appCfg['app'])) { throw new AE('[app] section required in ' . $file); }
                $array['app'][$app] = $appCfg['app'];
                $array['indexes'][$app] = isset($appCfg['app']['meta']['index']) ? (int) $appCfg['app']['meta']['index'] : 1000;
            }
        }
        
        $phpFile = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Generated/apps.php';
        $varExport = T::substituteConstants(var_export($array, true));
        $content = "<?php \n\n"
                 . "// Apps list generated file - dont edit\n"
                 . "// Generate with sma.php appgen\n\nreturn " 
                 . $varExport . ";\n";
        file_put_contents($phpFile, $content);
    }
    
    public static function generateMenu()
    {
        $apps = glob(APPLICATION_PATH . '/App/*');
        $menus = [];
        foreach ($apps as $dir) {
            $file = $dir . '/Config/menu.yml';
            if (file_exists($file)) {
                $app = strtolower(basename($dir));
                $data = Yaml::parseFile($file);
                $priority = isset($data['config']['priority']) ? (int) $data['config']['priority'] : 30;
                if (isset($data['config'])) {
                    unset($data['config']);
                }
                if (!$data) {
                    continue;
                }
                foreach (array_keys($data) as $key) {
                    $data[$key]['app'] = $app;
                }
                $menus[$priority][] = $data;
            }
        }
        ksort($menus);
        
        $output = [];
        foreach ($menus as $priority => $subMenus) {
            foreach ($subMenus as $menu) {
                $output = array_merge($output, $menu);
            }
        }
        
        $menuFile = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Generated/menu.php';
        $content = "<?php \n\n"
                 . "// Global menu generated file - dont edit\n"
                 . "// Generate with sma.php appgen\n\nreturn " 
                 . var_export($output, true) . ";\n";
        file_put_contents($menuFile, $content);
        
        $menuStrFile = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Generated/menu_strings.php';
        $content = "<?php \n\n// strings for translation\n\nreturn [\n";
        foreach (self::extractMenuStrings($output) as $string) {
            $content .= '    __("' . $string . '"),' . "\n";
        }
        $content .= "];\n";
        file_put_contents($menuStrFile, $content);
    }
    
    public static function generateGeneralConfig()
    {
        $generalConfigFile = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Config/profiles/general.yml';
        $targetConfigFile  = APPLICATION_PATH . '/App/' . Router::getDefaultControllerName(true) . '/Generated/general.php';
        $configTab = Yaml::parseFile($generalConfigFile);
        $content  = "<?php \n\n// General configuration file\n\nreturn ";
        $content .= var_export($configTab, true) . ";\n";
        file_put_contents($targetConfigFile, $content);
    }
    
    protected static function extractMenuStrings(array $menu)
    {
        static $strings = [];
        
        foreach ($menu as $item) {
            if (isset($item['label'])) {
                $strings[] = $item['label'];
            }
            if (isset($item['items']) && is_array($item['items'])) {
                self::extractMenuStrings($item['items']);
            }
        }
        return $strings;
    }
    
    public static function generateVersion()
    {
        if (!Application::isDevelopment()) { return false; }
        
        // Extraction de la révision svn
        $cmd = '/usr/bin/svn info ' . escapeshellarg(APP_PATH) 
             . " | grep -e '^R.vision.:.'"
             . " | cut -d' ' -f2";
        $subVersion = (int) `$cmd`;
        if (!$subVersion || $subVersion < 0 || $subVersion > 10000) {
            throw new ArchException('Bad subversion extraction [' . $subVersion . ']');
        }
        
        // Mise à jour du fichier version
        $versionFile = __DIR__ . '/Version.php';
        $content = file_get_contents($versionFile);
        $content = preg_replace('/SMA_SUBVERSION = [0-9]+;/', 'SMA_SUBVERSION = ' . $subVersion . ';', $content);
        
        // Simulation dans un fichier temporaire
        self::checkFileContentSyntax($content);
        
        // Ecriture du fichier de version
        file_put_contents($versionFile, $content);
        
        return true;
    }
    
    /**
     * Crée un fichier temporaire avec le contenu PHP et vérifie sa syntaxe
     * @param string $content
     * @return bool
     * @throws ArchException
     */
    public static function checkFileContentSyntax(string $content): bool
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'sma-test-');
        file_put_contents($tempFile, $content);
        $tempFileEscaped = escapeshellarg($tempFile);
        $noErr = 1 === (int) `/usr/bin/php -l $tempFileEscaped | grep 'No syntax error' | wc -l`;
        if (!$noErr) {
            throw new ArchException('Bad version file content syntax, see ' . $tempFile);
        }
        unlink($tempFile);
        return $noErr;
    }
}