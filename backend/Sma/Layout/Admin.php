<?php
namespace Sma\Layout;

use Sma\Layout;
use Osf\Helper\Tab;
use Osf\Navigation\Item;
use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;

/**
 * Admin Vue.js controller
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage layout
 */
class Admin extends Layout
{
    // To put for data to delete
    const DELETE = Tab::DELETE;
    
    const SIDEBAR              = 'sidebar';
    const SIDEBAR_MENU         = 'menu';
    const SIDEBAR_REGISTRATION = 'registration';
    const HEADER          = 'header';
    const HEADER_TITLE    = 'title';
    const HEADER_BUTTONS  = 'buttons';
    const HEADER_USER     = 'user';
    const HEADER_SETTINGS = 'settings';
    const PAGE         = 'page';
    const PAGE_TITLE   = 'title';
    const PAGE_LINKS   = 'links';
    const PAGE_ALERTS  = 'alerts';
    const PAGE_CONTENT = 'content';
    const PAGE_SCRIPTS = 'scripts';
    const FOOTER         = 'footer';
    const FOOTER_CONTENT = 'content';
    const FOOTER_LINKS   = 'links';
    const BTN_MSG   = 'msg';
    const BTN_NOTIF = 'ntf';
    const BTN_ALERT = 'alr';
    
    protected $menuItems = [];
    protected $forceRefreshBody = true;
    protected $forceRefreshScripts = true;
    
    public function __construct()
    {
        $this->layout = [
            self::SIDEBAR => [
                self::SIDEBAR_MENU => null,
                self::SIDEBAR_REGISTRATION => null
            ],
            self::HEADER => [
                self::HEADER_BUTTONS => [
                    self::BTN_MSG => ['type' => 'messages'],
                    self::BTN_NOTIF => ['type' => 'notifications'],
                    self::BTN_ALERT => ['type' => 'alerts']
                ],
                self::HEADER_USER => null,
                self::HEADER_SETTINGS => null
            ],
            self::PAGE => [
                self::PAGE_TITLE => null,
                self::PAGE_LINKS => [],
                self::PAGE_ALERTS => [],
                self::PAGE_CONTENT => null,
                self::PAGE_SCRIPTS => null
            ],
            self::FOOTER => [
                self::FOOTER_CONTENT => null,
                self::FOOTER_LINKS => []
            ]
        ];
        parent::__construct();
    }
    
    /**
     * @param Item $menu
     * @return $this
     */
    public function setMenu(Item $menu)
    {
        $this->layout['sidebar']['menu'] = $menu->toArray()['items'];
        return $this;
    }
    
    /**
     * @param string $label
     * @param string $subLabel
     * @param string $status
     * @param string $icon
     * @param string $imgsrc
     * @param string $imgalt
     * @param string $url
     * @return $this
     */
    public function updateRegistration(
            string $label    = null, 
            string $subLabel = null, 
            string $status   = null, 
            string $icon     = null, 
            string $imgsrc   = null, 
            string $imgalt   = null, 
            string $url      = null
            )
    {
        Tab::newArray($this->layoutPart([self::SIDEBAR, self::SIDEBAR_REGISTRATION]))
            ->addItemsIfNotNull(get_defined_vars());
        return $this;
    }
    
    /**
     * @return $this
     */
    public function cleanRegistration()
    {
        $this->layout[self::SIDEBAR][self::SIDEBAR_REGISTRATION] = null;
        return $this;
    }
    
    // =========================================================================
    // HEADER BUTTONS
    // =========================================================================
    
    /**
     * @param string $type
     * @param string $id
     * @param string $url
     * @param string $data
     * @return $this
     */
    protected function addButtonLink(string $type, string $id, ?string $url, string $data, array $vars = [])
    {
        $vars['id'] = $id;
        $vars['url'] = (string) $url;
        $vars['data'] = $data;
        $this->layout[self::HEADER][self::HEADER_BUTTONS][$type]['content'][] = $vars;
        return $this;
    }
    
    /**
     * @param string $type
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return $this
     */
    protected function updateButton(
            string $type          = null,
            string $title         = null, 
            string $icon          = null, 
            string $label         = null, 
            string $labelStatus   = null, 
            string $footLinkUrl   = null, 
            string $footLinkLabel = null
            )
    {
        $vars = get_defined_vars();
        unset($vars['type']);
        $types = [
            self::BTN_ALERT => 'tasks',
            self::BTN_MSG => 'messages',
            self::BTN_NOTIF => 'notifications'
        ];
        $vars['type'] = $types[$type];
        Tab::newArray($this->layoutPart([
            self::HEADER, 
            self::HEADER_BUTTONS, 
            $type
            ]))->addItemsIfNotNull($vars);
        return $this;
    }
    
    /**
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return $this
     */
    public function updateButtonMessages(
            string $title         = null, 
            string $icon          = null, 
            string $label         = null, 
            string $labelStatus   = null, 
            string $footLinkUrl   = null, 
            string $footLinkLabel = null
            )
    {
        return $this->updateButton(self::BTN_MSG, $title, $icon, $label, $labelStatus, $footLinkUrl, $footLinkLabel);
    }
    
    /**
     * @param string $id
     * @param string $url
     * @param string $data
     * @return $this
     */
    public function addButtonMessageLink(string $id, string $url, string $data)
    {
        return $this->addButtonLink(self::BTN_MSG, $id, $url, $data);
    }
    
    /**
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return $this
     */
    public function updateButtonNotifications(
            string $title         = null, 
            string $icon          = null, 
            string $label         = null, 
            string $labelStatus   = null, 
            string $footLinkUrl   = null, 
            string $footLinkLabel = null
            )
    {
        return $this->updateButton(self::BTN_NOTIF, $title, $icon, $label, $labelStatus, $footLinkUrl, $footLinkLabel);
    }
    
    /**
     * @param string $id
     * @param string $url
     * @param string $data
     * @return $this
     */
    public function addButtonNotificationsLink(string $id, ?string $url, string $label, ?string $icon = null, ?string $statusOrColor = null)
    {
        return $this->addButtonLink(self::BTN_NOTIF, $id, $url, $label, ['icon' => $icon, 'status' => $statusOrColor]);
    }
    
    /**
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return $this
     */
    public function updateButtonAlerts(
            string $title         = null, 
            string $icon          = null, 
            string $label         = null, 
            string $labelStatus   = null, 
            string $footLinkUrl   = null, 
            string $footLinkLabel = null
            )
    {
        return $this->updateButton(self::BTN_ALERT, $title, $icon, $label, $labelStatus, $footLinkUrl, $footLinkLabel);
    }
    
    /**
     * @param string $id
     * @param string $url
     * @param string $data
     * @return $this
     */
    public function addButtonAlertsLink(string $id, string $url, string $data)
    {
        return $this->addButtonLink(self::BTN_ALERT, $id, $url, $data);
    }
    
    /**
     * @param string $username
     * @param string $imgsrc
     * @param string $imgalt
     * @param string $title
     * @param string $subtitle
     * @param string $body
     * @param string $footer
     * @return $this
     */
    public function updateHeaderUser(
            string $username = null, 
            string $imgsrc   = null, 
            string $imgalt   = null,
            string $title    = null,
            string $subtitle = null,
            string $body     = null,
            string $footer   = null
            )
    {
        Tab::newArray($this->layoutPart([
            self::HEADER, 
            self::HEADER_USER
            ]))->addItemsIfNotNull(get_defined_vars());
        return $this;
    }
    
    /**
     * Titre et titre court généraux de l'application
     * @param string $title
     * @param string $shortTitle
     * @return $this
     */
    public function updateHeaderTitle(string $title, string $shortTitle)
    {
        Tab::newArray($this->layoutPart([
            self::HEADER, 
            self::HEADER_TITLE
            ]))->addItemsIfNotNull([$title, $shortTitle]);
        return $this;
    }
    
    /**
     * Clean all breadcrumb links
     * @return $this
     */
    public function cleanHeaderUser()
    {
        $this->layout[self::HEADER][self::HEADER_USER] = null;
        return $this;
    }
    
    public function updateSettings()
    {
        // @task [LAYOUT] settings management
    }
    
    /**
     * @param string $title
     * @param string $subtitle
     * @return $this
     */
    public function setPageTitle(string $title, string $subtitle = '')
    {
        Tab::newArray($this->layoutPart([
            self::PAGE
            ]))->addItemsIfNotNull(get_defined_vars(), true);
        return $this;
    }
    
    /**
     * Clean all breadcrumb links
     * @return $this
     */
    public function cleanBreadcrumb()
    {
        $this->layout[self::PAGE][self::PAGE_LINKS] = null;
        return $this;
    }
    
    /**
     * Add breadcrumb link
     * @param string $label
     * @param string $url
     * @return $this
     */
    public function addBreadcrumbLink(string $label, string $url)
    {
        $this->layoutPart([
            self::PAGE, 
            self::PAGE_LINKS
            ])[] = Tab::reduce(get_defined_vars(), ['label', 'url']);
        return $this;
    }
    
    /**
     * Clean all breadcrumb links
     * @return $this
     */
    public function cleanAlerts(): self
    {
        $this->layout[self::PAGE][self::PAGE_ALERTS] = null;
        return $this;
    }
    
    /**
     * Récupère le tableau d'alertes en cours
     * @return array|null
     */
//    public function getAlerts(): ?array
//    {
//        return isset($this->layout[self::PAGE][self::PAGE_ALERTS]) ? $this->layout[self::PAGE][self::PAGE_ALERTS] : null;
//    }
    
    /**
     * Clean all breadcrumb links
     * @return $this
     */
    public function cleanButtons()
    {
        $this->layout[self::HEADER][self::HEADER_BUTTONS] = null;
        return $this;
    }
    
    /**
     * Add an alert to the current page
     * @param string $title
     * @param string $message
     * @param string $status
     * @param bool $closable
     * @return $this
     */
    public function addAlert(string $title = null, string $message = null, string $status = AVH::STATUS_INFO, bool $closable = true)
    {
        $this->layoutPart([
            self::PAGE,
            self::PAGE_ALERTS
            ])[] = Tab::reduce(get_defined_vars(), ['title', 'message', 'status', 'closable']);
        return $this;
    }
    
    /**
     * @param string $message
     * @param string $title
     * @return $this
     */
    public function info(string $message, string $title = null)
    {
        return $this->addAlert($title, $message, AVH::STATUS_INFO, true);
    }
    
    /**
     * @param string $message
     * @param string $title
     * @return $this
     */
    public function warning(string $message, string $title = null)
    {
        return $this->addAlert($title, $message, AVH::STATUS_WARNING, true);
    }
    
    /**
     * @param string $message
     * @param string $title
     * @return $this
     */
    public function error(string $message, string $title = null)
    {
        return $this->addAlert($title, $message, AVH::STATUS_DANGER, true);
    }
    
    /**
     * @param string $message
     * @param string $title
     * @return $this
     */
    public function success(string $message, string $title = null)
    {
        return $this->addAlert($title, $message, AVH::STATUS_SUCCESS, true);
    }
    
    /**
     * Set HTML page content (body). If null, do nothing.
     * @param string $content
     * @return $this
     */
    public function setPageContent($content)
    {
        if ($content !== false) {
            $this->layout[self::PAGE][self::PAGE_CONTENT] = $content === null ? null : (string) $content;
        }
        return $this;
    }
    
    /**
     * Clean common javascript
     * @return $this
     */
    public function cleanScripts()
    {
        $this->layout[self::PAGE][self::PAGE_SCRIPTS] = null;
        return $this;
    }
    
    /**
     * Append common javascript
     * @param string $script
     * @return $this
     */
    public function appendScripts(string $script)
    {
        $this->layout[self::PAGE][self::PAGE_SCRIPTS] .= $script;
        return $this;
    }

    /**
     * Set HTML footer content (copyright)
     * @param string $content
     * @return $this
     */
    public function setFooterContent(string $content)
    {
        $this->layout[self::FOOTER][self::FOOTER_CONTENT] = $content;
        return $this;
    }
    
    /**
     * @return $this
     */
    public function cleanFooterLinks()
    {
        $this->layout[self::FOOTER][self::FOOTER_LINKS] = null;
        return $this;
    }
    
    /**
     * @param string $label
     * @param string $url
     * @return $this
     */
    public function addFooterLink(string $label, string $url)
    {
        $this->layoutPart([
            self::FOOTER, 
            self::FOOTER_LINKS
            ])[] = Tab::reduce(get_defined_vars(), ['label', 'url']);
        return $this;
    }
    
    // TOOLS
    
    /**
     * Get a reference link to a sub-part of the layout array
     * @param string $keys
     * @return array
     */
    protected function &layoutPart(...$keys)
    {
        $array = &$this->layout;
        foreach ($keys[0] as $key) {
            if (!array_key_exists($key, $array) || $array[$key] === null) {
                $array[$key] = [];
            }
            $array = &$array[$key];
        }
        if (!$array) {
            $array = [];
        }
        return $array;
    }
    
    /**
     * Réinitialisation des alertes avant mise en session
     * @return $this
     */
    protected function registerLayout()
    {
        $layout = $this->layout;
        $layout[self::PAGE][self::PAGE_ALERTS] = '';
        $layout[self::PAGE][self::PAGE_LINKS]  = '';
        $this->getSession()->layout = $layout;
        return $this;
    }
    
    /**
     * Si le body n'a pas changé, le renvoyer quand même
     * @param bool $trueOrFalse
     * @return $this
     */
    public function forceRefreshBody($trueOrFalse = true)
    {
        $this->forceRefreshBody = (bool) $trueOrFalse;
        return $this;
    }
    
    /**
     * Si les scripts n'ont pas changer, les envoyer quand même (exécuter quoi qu'il arrive)
     * @param bool $trueOrFalse
     * @return $this
     */
    public function forceRefreshScripts($trueOrFalse = true)
    {
        $this->forceRefreshScripts = (bool) $trueOrFalse;
        return $this;
    }
    
    /**
     * Supprime le contenu de la page (body) dans la page courante ET la session
     * @return $this
     */
    public function clearPageContent()
    {
        $this->layout[self::PAGE][self::PAGE_CONTENT] = null;
        $session = $this->getSession();
        $layout = $session->get('layout');
        if ($layout && isset($layout[self::PAGE]) && isset($layout[self::PAGE][self::PAGE_CONTENT])) {
            $layout[self::PAGE][self::PAGE_CONTENT] = null;
            $session->set('layout', $layout);
        }
        return $this;
    }
    
    /**
     * Surcharge de la fabrication des données à envoyées, ré-envoi du content si nécessaire.
     * @param string $renderType
     * @return array
     */
    protected function getComputedRenderData($renderType): array
    {
        $data = parent::getComputedRenderData($renderType);
        
        // Force refreshs
        if ($this->forceRefreshBody && isset($data[self::DO_UPDATE]) && isset($this->layout[self::PAGE][self::PAGE_CONTENT])) {
            $data[self::DO_UPDATE][self::PAGE][self::PAGE_CONTENT] = $this->layout[self::PAGE][self::PAGE_CONTENT];
        }
        if ($this->forceRefreshScripts && isset($data[self::DO_UPDATE]) && isset($this->layout[self::PAGE][self::PAGE_SCRIPTS])) {
            $data[self::DO_UPDATE][self::PAGE][self::PAGE_SCRIPTS] = $this->layout[self::PAGE][self::PAGE_SCRIPTS];
        }
        
        // L'update du state des notifications pose problème sur le front, 
        // on est obligé de renvoyer toutes les notifs pour l'instant
        if (isset($data[self::DO_UPDATE][self::HEADER][self::HEADER_BUTTONS])) {
            $data[self::DO_UPDATE][self::HEADER][self::HEADER_BUTTONS] = $this->layout[self::HEADER][self::HEADER_BUTTONS];
        }
        
        return $data;
    }
}
