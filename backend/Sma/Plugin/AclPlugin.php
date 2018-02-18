<?php
namespace Sma\Plugin;

use Zend\Permissions\Acl\Exception\InvalidArgumentException;
use Osf\Application\PluginAbstract;
use Osf\Exception\DisplayedException;
use Osf\Exception\ArchException;
use Sma\Session\Identity;
use Sma\Container;
use Sma\Log;
use App\Account\Model\Auth;
use ACL;

/**
 * Plugin for SMA application
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage acl
 */
class AclPlugin extends PluginAbstract
{
    public function beforeAction()
    {
        // Test effectué sur le controleur et l'action courante
        $controller = Container::getRequest()->getController();
        $action     = Container::getRequest()->getAction();
        
        // Vérification & erreurs
        try {
            if (!ACL::isAllowedParams($controller, $action)) {
                if ($controller === 'guest') {
//                    Log::info("Accès à guest sans authentification (session timeout ou accès interdit)");
//                    throw new DisplayedException(__("Votre session est terminée ou cet accès nécessite une authentification. Veuillez utiliser le lien reçu par e-mail."));
                } else if (!Identity::isLogged()) {
                    Auth::logout();
                    Container::getFlashMessenger()->msgWarning($this->get404Msg());
                    Container::getFlashMessenger()->setRedirectToParams(Container::getRequest()->getParams(true));
                    Container::getRequest()->reset()->setController('account')->setAction('login');
                } else {
                    Log::hack("Tentative d'accès à une action non autorisée");
                    throw new ArchException(__("Tentative d'accès à une fonctionnalité non autorisée."));
                }
            }
        } catch (InvalidArgumentException $e) {
            throw new DisplayedException(__("Ce contenu n'existe pas ou n'est plus disponible."), 404);
        }
    }
    
    protected function get404Msg()
    {
        return __("Cette page n'est pas disponible hors connexion. Veuillez vous identifier pour avoir accès à cette fonctionnalité.");
    }
}
