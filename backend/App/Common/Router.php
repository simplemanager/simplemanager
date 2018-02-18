<?php
namespace App\Common;

use App\Common\Container;
use Osf\Controller\Router as OsfRouter;

/**
 * Specific application router (bind to osf router)
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 14 sept. 2013
 * @package app
 * @subpackage common
 */
class Router extends OsfRouter
{
    public function route($uri = null)
    {
        // Bug ? VueJS qui ne route.push pas l'url avec son querystring, 
        // même avec la notation {path: ..., query: ...}
        $uri = $uri !== null ? $uri : $this->getAppUri();
        $uri = str_replace('/frsh/', '?frsh=', $uri);
        
        // Appel du routage par défaut par défaut
        parent::route($uri);
        $request = Container::getRequest();
        
        // INFO
        if ($request->getController() === 'info' && $request->getAction() !== 'index') {
            $document = strtr($request->getAction(), '_', '/');
            $request->setParam('document', $document);
            $request->setAction('display');
        }
        
        // Document checker
        if ($request->getController() === 'check' && preg_match('/^[0-9a-f]{64}$/', $request->getAction())) {
            $code = $request->getAction();
            $request->setController('event')
                    ->setAction('check')
                    ->setParams(['code' => $code]);
        }
    }

    public function buildUri(array $params = null, $controller = null, $action = null, $prepareUri = true)
    {
        // Récupération des paramètres réels
        if ($prepareUri) {
            [$params, $controller, $action] = $this->prepareUri($params, $controller, $action);
        }

        // @task [ROUTER] Contrôleur "info" pour les pages statiques
//        if ($controller == 'info' && $action !== 'index') {
//            $doc = [];
//            if ($action) {
//                $doc[] = $action;
//            }
//            if (isset($params['document'])) {
//                $doc[] = $params['document'];
//                unset($params['document']);
//            }
//            $uri = '/info/' . implode('/', $doc);
//            return rtrim($this->getBaseUrl(), ' /') . $uri;
//        }
        
        // Construction de l'URI par défaut
        return parent::buildUri($params, $controller, $action, false);
    }
}