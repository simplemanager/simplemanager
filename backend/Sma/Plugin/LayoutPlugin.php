<?php
namespace Sma\Plugin;

use Osf\Application\PluginAbstract as Plugin;
use Osf\View\Helper\Bootstrap\Grid;
use Osf\Stream\Text as T;
use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\Application\OsfApplication as Application;
use Sma\Session\Identity as I;
use Sma\Version;
use Sma\Image;
use Sma\Acl as SmaAcl;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use App\Guest\Controller as GuestController;
use App\Common\Container;
use H, L, ACL;

/**
 * Layout management
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage plugin
 */
class LayoutPlugin extends Plugin
{
    public function beforeDispatchLoop()
    {
        // Si la réponse n'est pas du json, on ne fait pas ces opérations
        if (Container::getResponse()->getType() !== 'json') {
            return ;
        }
        
        // Réinitialisation de la requête json
        Container::getJsonRequest()
                ->cleanFooterLinks()
                ->cleanScripts()
                ->updateHeaderTitle(APP_NAME, APP_SNAM)
                ->addFooterLink('FAQ', H::url('info', 'faq'))
                ->addFooterLink(__("Conditions"), H::url('info', 'conditions'))
                ->setFooterContent('&copy; ' . date('Y') . ' ' . 
                        H::html('<a href="/">' . APP_NAME . '</a> - V' . Version::getSmaVersion())->escape(false));
        
        // Réinitialisation des alertes, titres, etc. et MAJ du timestamp si ce n'est pas une requête tick
        if (!self::isTickRequest()) {
            Container::getJsonRequest()
                    ->cleanAlerts()
                    ->cleanBreadcrumb()
                    ->setPageTitle('');
            I::updateTimestamp();
        }
    }
    
    // @task [PERF] régénérer le menu et le layout que si un signal de modification a été lancé
    public function afterDispatchLoop()
    {   
        // Si la réponse n'est pas du json, on ne fait pas ces opérations
        if (Container::getResponse()->getType() !== 'json') {
            return ;
        }
        
        $request = Container::getJsonRequest();
        
        // Mise à jour du menu
        if (I::isLogged()) {
            $iCompletion = I::getCompletion();
            L::getMenu()->getItem('prf')->addBadge($iCompletion . '%', AVH::getPercentageColor($iCompletion));
            $request->addFooterLink('Guide', H::url('info', 'book'));
        }
        
        // Mise à jour du layout
        if (I::isLogged()) {
            $values = I::getAll();
            $idLogo = I::get('company', 'id_logo');
            $url = $idLogo ? Image::getImageUrl($idLogo, 800) : null;
            
            $links = [
                (string) H::link(__("Société"),    'account', 'company') ->setTooltip(__("Options de mon entreprise")),
                (string) H::link(__("Compte"),     'account', 'login')   ->setTooltip(__("Modifier mes infos")),
                (string) H::link(__("Paramètres"), 'account', 'features')->setTooltip(sprintf(__("Paramètres %s"), APP_NAME)),
            ];
            $grid = new Grid();
            $headBody = $grid->gridStatic()->auto($links, count($links), false, ['text-center']);
            if (!Container::getDevice()->isMobile()) {
                $connected = I::getTimestampWarn() ? __("Inactif...") : __("Connecté");
                $conColor  = I::getTimestampWarn() ? 'yellow' : 'green';
                $request->updateRegistration(
                            T::crop($values['firstname'], 20), $connected, $conColor, 
                            ($url ? null : 'user'), $url, null, H::url());
            }
            $request->updateHeaderUser(
                            T::crop($values['firstname'], 30), 
                            $url, 
                            I::get('company', 'title'), 
                            I::get('company', 'title'), 
                            $values['firstname'] . ' ' . $values['lastname'], 
                            $headBody,
                            (string) H::button(__("Déconnexion"), H::url('account', 'logout'))->statusPrimary());
        } else if (!GuestController::isLogged()) {
            $request->cleanRegistration()->cleanHeaderUser();
        }
        
        // Chargement, mise à jour du menu principal
        self::loadMenu();
        
        // Affichage des messages flash et les notifications
        // Puis sauvegarde de l'état du FlashMessenger avant l'envoi des données
        Container::getFlashMessenger()->sendMessages()->save();
    }
    
    protected static function isTickRequest(): bool
    {
        return Container::getRequest()->getController() === 'event' &&
               Container::getRequest()->getAction()     === 'tick';
    }
    
    /**
     * Load menu
     * @staticvar \Osf\Navigation\Item $menu
     * @param bool $reload
     * @return \Osf\Navigation\Item
     */
    public static function loadMenu(bool $reload = false)
    {
        static $menu = null;
        
        // Add menu in layout configuration
        if ($reload || $menu === null) {
            $menu = Container::getNavigationMenu();
            $commonMenu = include APPLICATION_PATH . '/App/Common/Generated/menu.php';
            if (LTM::isActive()) {
                $commonMenu['ld']['label'] = __("Lettres & Modèles");
            }
            switch (ACL::getCurrentRole()) {
                case SmaAcl::ROLE_GUEST : 
                    $apps = ['guest'];
                    break;
                default : 
                    $apps = [
                        'account', 'admin', 'sandbox', 'recipient', 
                        'product', 'invoice', // 'guest', //'survey'
                    ];
            }
            
            if (Application::isDevelopment()) { $apps[] = 'dev'; }
            $apps = array_merge($apps, I::getFeatures());
            $menu->clean()->importChilds($commonMenu, Container::getAcl(), [], $apps, Container::getAcl()->getCurrentRole());
        }
        return $menu;
    }
}
