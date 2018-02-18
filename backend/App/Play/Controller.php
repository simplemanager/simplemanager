<?php
namespace App\Play;

use Sma\Controller\Json as JsonAction;
use Sma\Session\Identity;

/**
 * Sma Play
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 déc. 2013
 * @package common
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    protected $pagesDir = null;
    
    public function indexAction()
    {
        if (Identity::isLevelBeginner()) {
            $this->alertInfo(
                __("Qu'est-ce qu'une application ?"),
                sprintf(__("Les applications apportent des fonctionnalités à votre interface %s, un peu comme celles de votre smartphone. Choisissez ici les applications %s qui correspondent à vos besoins."), APP_NAME, APP_SNAM));
        }
        $apps = __DIR__ . '/../Common/Generated/apps.php';
        return ['apps' => (include $apps)];
    }
}
