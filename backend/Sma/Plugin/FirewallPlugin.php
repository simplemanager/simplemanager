<?php
namespace Sma\Plugin;

use Osf\Application\PluginAbstract;
use Sma\Container as C;

/**
 * Firewall general plugin
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage application
 */
class FirewallPlugin extends PluginAbstract
{
    public function beforeRoute()
    {
        C::getFirewall()->check();
    }
}
