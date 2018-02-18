<?php
namespace App\Common;

use Osf\Container\PluginManager;
use Osf\View\Component;
use Osf\Exception\Error;
use Sma\Plugin\FirewallPlugin;
use Sma\Plugin\LayoutPlugin;
use Sma\Plugin\AclPlugin;
use Sma\Plugin\LogPlugin;
use App\Common\Container;

/**
 * Dynamic initialisation
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 12 sept. 2013
 * @package common
 * @subpackage bootstrap
 */
class Bootstrap extends \Osf\Application\Bootstrap
{
    const LOCALES = [
        'fr' => 'fr_FR',
        'en' => 'en_US'
    ];
    
    public function bootstrap()
    {
        $this->initErrorHandler();
        $this->initBootstrap();
        $this->initResponse();
        $this->initLocale();
        $this->initTranslate();
        $this->initPlugins();
    }

    public function initErrorHandler()
    {
        Error::startErrorHandler();
    }
    
    /**
     * Twitter bootstrap manager
     */
    public function initBootstrap()
    {
        Component::getBootstrap();
    }
    
    public function initResponse()
    {
        Container::getResponse()->setTypeJson();
    }
    
    public function initLocale()
    {
        $locale = $this->buildLocale();
        if (!in_array($locale->getDefault(), self::LOCALES)) {
            $locale->setDefault(self::LOCALES['fr']);
        }
      	//date_default_timezone_set('Europe/Paris');
    }
    
    public function initTranslate()
    {
        $this->buildTranslate();
    }
    
    // Layout lazy initialisation
    // Automatiquement appelé par le container après le chargement de l'objet "Layout"
    public function afterBuildLayout()
    {
    }
    
    public function initPlugins()
    {
        PluginManager::registerApplicationPlugin(new FirewallPlugin());
        PluginManager::registerApplicationPlugin(new LayoutPlugin());
        PluginManager::registerApplicationPlugin(new AclPlugin());
        PluginManager::registerApplicationPlugin(new LogPlugin());
    }
}
