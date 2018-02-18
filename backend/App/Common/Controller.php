<?php
namespace App\Common;

use App\Guest\Controller as GuestController;
use Sma\Controller\Json as JsonAction;
use App\Account\Form\FormLogin;

/**
 * Home page
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 13 sept. 2013
 * @package www
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    public function indexAction()
    {
        if (GuestController::isLogged()) {
            $this->alertInfo(
                    __("Vous êtes connecté à l'espace invité"),
                    __("Utilisez le menu pour revenir sur votre espace invité ou cliquez sur 'Déconnexion' pour quitter cet espace."));
        }
        $form = new FormLogin();
        return ['form' => $form];
    }
}
