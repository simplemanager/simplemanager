<?php
namespace Sma\Controller;

use Osf\Exception\HttpException;
use Osf\Controller\Action;
use Osf\View\Component;
use App\Common\Container;

/**
 * Controleur de type JSON
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage controller
 */
class Json extends Action
{
    use Addon\Alert;
    use Addon\Exchange;
    
    const REDIRECT_AUTO_PARAM = 'rd';
    
    public function __construct()
    {
        static $setted = false;
        
        if (!$setted) {
            $scripts = Component::getVueJs()->getAjaxScripts();
            Container::getJsonRequest()->appendScripts($scripts);
            $setted = true;
        }
        parent::__construct();
    }
    
    /**
     * @return \Sma\Layout\Admin
     */
    public function layout()
    {
        return Container::getJsonRequest();
    }
    
    public function pageTitle(string $title, string $subTitle = '')
    {
        $this->layout()->setPageTitle($title, $subTitle);
        
    }
    
    /**
     * Ajax redirect
     * @param string $url
     * @return $this
     */
    public function redirect(string $url, bool $withRouter = true)
    {
        // On désactive la construction de la vue courante
        $this->disableView();
        
        // On désactive l'affichage des messages d'alertes
        Container::getFlashMessenger()->skipThisRequest();
        
        // On supprime tout contenu de page et les scripts
        Container::getJsonRequest()->clearPageContent()->forceRefreshScripts();
        
        // On annule l'exécution des éventuels scripts de la page courante
        Component::getJquery()->clearScripts();
        
        // On enregistre le script de redirection
        if ($withRouter) {
            Component::getVueJs()->redirect($url);
        } else {
            Component::getVueJs()->ajaxCall($url, null, false, true);
        }
        
        return $this;
    }
    
    /**
     * Redirection automatique vers l'url demandée en paramètre si elle existe
     * @param string $defaultUrl
     * @param bool $withRouter
     * @return $this
     */
    public function redirectAuto(string $defaultUrl = null, bool $withRouter = true)
    {
        // Détermine l'url pour la redirection
        $url = self::decodeUri($this->getParam(self::REDIRECT_AUTO_PARAM)) 
                ?? $defaultUrl 
                ?? null;
        
        // Redirection
        return $url ? $this->redirect($url, $withRouter) : $this;
    }
    
    /**
     * Appel ajax bas niveau (bypasse le routeur)
     * @param string $url
     * @param string $target
     * @param bool $replaceTag
     * @param string|bool $waitTarget true = sablier affiché au bout de 500ms si connexion lente sur $target
     * @return $this
     */
    public function ajaxCall(string $url, string $target = null, bool $replaceTag = false, $waitTarget = true)
    {
        Component::getVueJs()->ajaxCall($url, $target, $replaceTag, $waitTarget);
        Container::getJsonRequest()->clearPageContent();
        return $this;
    }
    
    /**
     * Open URL in popup
     * @param string $url
     * @return $this
     */
    public function popup(string $url)
    {
        //$this->disableView();
        Component::getVueJs()->popup($url);
        return $this;
    }
    
    /**
     * Reinit the dispatch loop with a new request
     * @param array $params
     * @return $this
     */
    public function dispatch(array $params = [])
    {
        parent::dispatch($params);
        //Container::getResponse()->setRawHeader('Content-type: application/json; charset=utf-8');
        Container::getJsonRequest();
        return $this;
    }
    
    public function preDispatch()
    {
        $this->getResponse()->setTypeJson();
    }
    
    /**
     * To put if feature is not available
     */
    public function todo()
    {
        $this->disableView();
        $this->alertWarning(__("Fonctionnalité en travaux"), __("Cette fonctionnalité n'est pas encore disponible car en cours de développement ou maintenance."));
        Container::getJsonRequest()->setPageContent('');
    }
    
    /**
     * Launch "Page not found"
     */
    public function notFound()
    {
        throw new HttpException("Page not found", 404);
    }
    
    /**
     * Quand on désactive le layout, la génération json est désactivée
     */
    public function disableLayout() {
        parent::disableLayout();
        $this->getResponse()->setTypeHtml();
    }
    
    /**
     * Encodage commun des paramètres uri
     * @param string $uri
     * @return string
     */
    public static function encodeUri(?string $uri): ?string
    {
        return $uri === null ? null : rawurlencode(rawurlencode($uri));
    }
    
    /**
     * Décodage commun des paramètres uri
     * @param string $encryptedUri
     * @return string
     */
    public static function decodeUri(?string $encryptedUri): ?string
    {
        return $encryptedUri === null ? null : rawurldecode(rawurldecode($encryptedUri));
    }
    
    /**
     * Version encodée de l'uri courante
     * @return string
     */
    public static function encodedCurrentUri(): string
    {
        return self::encodeUri(Container::getRequest()->getUri(false, true));
    }
}