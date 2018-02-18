<?php
namespace Sma\Plugin;

use Osf\Application\PluginAbstract as Plugin;
use Sma\Log;
use Sma\Container;

/**
 * Automatic logging
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage plugin
 */
class LogPlugin extends Plugin 
{
    public function beforeAction()
    {
        $controller = Container::getRequest()->getController();
        $action = Container::getRequest()->getAction();
        $ctrlAction = $controller . '_' . $action;
        if (!in_array($ctrlAction, ['event_tick', 'dev_comment'])) {
            $msg = $controller . ':' . $action;
            Log::info($msg, 'PAGE');
        }
    }
}
