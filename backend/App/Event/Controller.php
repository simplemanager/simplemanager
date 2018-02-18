<?php
namespace App\Event;

use Osf\Exception\DisplayedException;
use Osf\Exception\AlertException;
use Osf\Exception\HttpException;
use Osf\Application\OsfApplication as Application;
use Osf\Stream\Json;
use Sma\Controller\Json as JsonAction;
use Sma\Bean\InvoiceBean as IB;
use Sma\Db\DbRegistry;
use Sma\Layout;
use Sma\Log;
use Sma\Session\Identity;
use App\Account\Model\Auth;
use App\Common\Container;
use L, H, ACL, DB;

/**
 * Special event controller (error handling)
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 26 sept. 2013
 * @package common
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    const INACTIVITY_WARN_SECONDS   = 50 * 60;
    const INACTIVITY_LOGOUT_SECONDS = 60 * 60;
    
    public function errorAction()
    {
        Container::getResponse()->clearHeaders()->clearType()->setTypeHtml();
        Container::getApplication()->setDispatchStep(Application::RENDER_VIEW, true);
        $pageTitle = 'Oops !';
        $retVals = ['title' => null];
        /* @var $exception \Osf\Exception */
        $exception = Container::getView()->getValue('exception');
        switch (true) {
            case $exception instanceof HttpException :
                switch ($exception->getCode()) {
                    case 404 :
                        $retVals['title'] = __("Cette page n'existe pas (404)");
                        $retVals['description'] = __("Il semble que ce contenu n'existe pas ou n'est plus disponible.");
                        break;
                    case 301 :
                        $retVals['title'] = __('Redirection'); 
                        $retVals['description'] = 'Not implemented yet';
                        break;
                }
                Log::error($exception->getMessage(), 'EXCEPT', $exception->getTraceAsString());
                break;
            case $exception instanceof AlertException : 
                $this->disableView();
                Container::getResponse()
                        ->reset()
                        ->setTypeJson();
                H::layout()
                        ->cancelUpdates()
                        ->addAlert($exception->getTitle(), $exception->getMessage(), $exception->getStatus());
                $pageTitle = null;
                Log::warning($exception->getMessage(), 'ALERT', $exception->getTraceAsString());
                break;
            case $exception instanceof DisplayedException : 
                $retVals['description'] = $exception->getMessage();
                Log::warning($exception->getMessage(), 'DISPLAYED', $exception->getTraceAsString());
                break;
            default : 
                $retVals['description'] = __("Une erreur indépendante de notre volonté est survenue, nous nous efforçons de la corriger au plus vite. Veuillez nous excuser pour la gêne occasionnée.");
                Log::warning($exception->getMessage(), 'UNKNOWN', $exception->getTraceAsString());
        }
        $pageTitle !== null && L::setPageTitle($pageTitle);
        return $retVals;
    }
    
    // @task: sécuriser l'utilisation du query string
    public function initAction()
    {
        $this->disableView();
        $qs = filter_input(INPUT_SERVER, 'QUERY_STRING');
        Container::getJsonRequest()->setRenderType(Layout::RENDER_INIT);
        if (!preg_match('#^[/a-zA-Z0-9?&=_-]+$#', $qs)) {
            Log::hack('Requête invalide', $qs);
            $this->dispatchUri('/event/notfound');
            return [];
        }
        Container::getRouter()->route($qs);
        if (!ACL::hasResourceParams(
                Container::getRequest()->getController(), 
                Container::getRequest()->getAction())) {
            Log::error('Requête introuvable (404)', '404', $qs);
            $this->dispatchUri('/event/notfound');
        } else {
            Identity::isLogged() && self::registerTick();
            $this->dispatchUri($qs, Container::getRouter());
        }
        return [];
    }
    
    public function notfoundAction()
    {
        Container::getJsonRequest()
                ->cleanAlerts()
                ->cleanBreadcrumb()
                ->clearPageContent();
    }
    
    /**
     * Appel effectué par le client toutes les minutes
     */
    public function tickAction()
    {
        $this->disableView();
        Container::getJsonRequest()->forceRefreshBody(false);
        if (Identity::isLogged()) {
            $this->launchTick();
        }
    }
    
    protected function launchTick()
    {
        if (Identity::isLogged()) {
            $duration = time() - Identity::getTimestampLast();
            if ($duration > self::INACTIVITY_LOGOUT_SECONDS) {
                Auth::logout();
                $this->alertWarning(
                    __("Votre session est expirée."), 
                    sprintf(__("Pour continuer sur %s veuillez vous ré-identifier."), APP_NAME));
                $this->redirect(H::url('account', 'login'));
                return;
            } else if ($duration > self::INACTIVITY_WARN_SECONDS && !Identity::getTimestampWarn()) {
                $this->alertWarning(
                    __("Êtes-vous toujours là ?"), 
                    sprintf(__("Voilà %d minutes que vous n'avez rien fait. Votre session va se terminer automatiquement d'ici %d minutes d'inactivité."), 
                            self::INACTIVITY_WARN_SECONDS / 60, 
                            (self::INACTIVITY_LOGOUT_SECONDS - self::INACTIVITY_WARN_SECONDS) / 60));
                Identity::setTimestampWarn(true);
            }
            DbRegistry::notificationUpdate();
            self::registerTick();
        } else {
            $this->redirect(H::url('account', 'login'));
        }
    }
    
    // Appelé par tickAction et loginAction pour lancer les ticks
    public static function registerTick()
    {
        $url = H::url('event', 'tick'); //, ['t' => time()]);
        $ms = 10000 * (Application::isDevelopment() ? 1 : 6);
        H::layout()->appendScripts('window.setTimeout(function(){$.ajaxCall("' . $url . '")},' . $ms . ');');
    }
    
    /**
     * Upload de fichiers
     */
    public function uploadAction()
    {
        $this->disableViewAndLayout();
    }
    
    /**
     * Autocomplete
     */
    public function acAction()
    {
        $params = $this->getParams();
        if (count($params) == 1) {
            $this->json(Container::getSearch()->searchAutocomplete(urldecode(array_values($params)[0]), array_keys($params)[0]));
        } else {
            $this->json('[]');
        }
    }
    
    /**
     * Appel du moteur de recherche
     */
    public function searchAction()
    {
        $this->disableViewAndLayout();
        $query = urldecode($this->getParam('query'));
        $toSend = [];
        if ($query !== null) {
            $results = Container::getSearch()->search($query, null, true);
            foreach ($results as $row) {
                $icon = 'file-text-o';
                $color = null;
                switch (true) {
                    case preg_match('#^.*/product/view/.*$#', $row['url']) : 
                        $icon = 'shopping-basket';
                        $color = 'blue';
                        break;
                    case preg_match('#^/recipient/view/.*$#', $row['url']) :
                        $icon = 'user-o';
                        $color = 'orange';
                        break;
                    case preg_match('#^/document/view/.*$#', $row['url']) :
                        $icon = 'envelope-o';
                        $color = 'green';
                        break;
                    case preg_match('#^/invoice/view/type/' . IB::TYPE_QUOTE . '/.*$#', $row['url']) :
                        $icon = 'file-o';
                        $color = 'primary';
                        break;
                    case preg_match('#^/invoice/view/type/' . IB::TYPE_ORDER . '/.*$#', $row['url']) :
                        $icon = 'file-text-o';
                        $color = 'primary';
                        break;
                    case preg_match('#^/invoice/view/type/' . IB::TYPE_INVOICE . '/.*$#', $row['url']) :
                        $icon = 'file-text';
                        $color = 'primary';
                        break;
                }
                $toSend[] = [$row['title'], $row['url'], $icon, $color];
            }
        }
        $this->json(Json::encode($toSend));
    }
    
    /**
     * Suppression d'une notification
     */
    public function rmnotAction()
    {
        $this->disableViewAndLayout();
        $id = (int) $this->getParam('id');
        if (!$id) {
            return 0;
        }
        echo (int) DbRegistry::notificationRemoveOne($id);
    }
    
    /**
     * Clic sur "j'ai compris" d'une alerte
     */
    public function iknowAction()
    {
        $this->disableView();
        Container::getJsonRequest()->forceRefreshBody(false);
        $hash = $this->getParam('hash');
        //$this->alertInfo('OK, suppression de ' . $hash);
    }
    
    public function checkAction()
    {
        $code = $this->getParam('code');
        $row = DB::getDocumentHistoryTable()->select(['hash' => $code])->current();
        $bean = $row ? $row->getBean() : null;
        return ['bean' => $bean, 'hash' => $code];
    }
    
    /**
     * Message de type alerte
     */
    public function msgAction()
    {
        $this->disableView();
        Container::getJsonRequest()->forceRefreshBody(false);
        $key = $this->getParam('k');
        switch ($key) {
            case 'is' : // Invoice Status
                $this->alertWarning(__("État non modifiable"), __("Les conditions d'inaltérabilité interdisent cette action."));
                break;
            case 'ds' : // Document Status
                $this->alertInfo(__("Modification d'état"), __("L'état de ce document ne peut être modifié manuellement."));
                break;
            default : 
        }
        return [];
    }
}
