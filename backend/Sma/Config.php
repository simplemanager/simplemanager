<?php
namespace Sma;

use Osf\Config\OsfConfig;
use Osf\Stream\Yaml;
use App\Common\Container;
use Sma\Session\Identity;
use C;

/**
 * Gestionnaire de paramètres
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage config
 */
class Config extends OsfConfig
{
    const CONFIG_FILE = '/App/Common/Config/profiles/general.yml';
    const CACHE_KEY = 'GENERAL_CONFIG_FILE';
    
    public function __construct() {
        $isDev = Container::getApplication()->isDevelopment();
        if ($isDev || !($config = C::get(self::CACHE_KEY))) {
            $file = APPLICATION_PATH . self::CONFIG_FILE;
            $config = Yaml::parseFile($file);
            $isDev || C::set(self::CACHE_KEY, $config);
        }
        $this->appendConfig($config, Identity::getAll());
    }
}
