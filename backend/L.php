<?php

use App\Common\Container;
use Sma\Layout\AbstractLayoutContainer;

/**
 * Json layout quick access + Mobile detect
 *
 * This class is generated, do not edit it
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class L extends AbstractLayoutContainer
{

    /**
     * @param Item $menu
     * @return \Sma\Layout\Admin
     */
    public static function setMenu(\Osf\Navigation\Item $menu)
    {
        return Container::getJsonRequest()->setMenu($menu);
    }

    /**
     * @param string $label
     * @param string $subLabel
     * @param string $status
     * @param string $icon
     * @param string $imgsrc
     * @param string $imgalt
     * @param string $url
     * @return \Sma\Layout\Admin
     */
    public static function updateRegistration(string $label = null, string $subLabel = null, string $status = null, string $icon = null, string $imgsrc = null, string $imgalt = null, string $url = null)
    {
        return Container::getJsonRequest()->updateRegistration($label, $subLabel, $status, $icon, $imgsrc, $imgalt, $url);
    }

    /**
     * @return \Sma\Layout\Admin
     */
    public static function cleanRegistration()
    {
        return Container::getJsonRequest()->cleanRegistration();
    }

    /**
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return \Sma\Layout\Admin
     */
    public static function updateButtonMessages(string $title = null, string $icon = null, string $label = null, string $labelStatus = null, string $footLinkUrl = null, string $footLinkLabel = null)
    {
        return Container::getJsonRequest()->updateButtonMessages($title, $icon, $label, $labelStatus, $footLinkUrl, $footLinkLabel);
    }

    /**
     * @param string $id
     * @param string $url
     * @param string $data
     * @return \Sma\Layout\Admin
     */
    public static function addButtonMessageLink(string $id, string $url, string $data)
    {
        return Container::getJsonRequest()->addButtonMessageLink($id, $url, $data);
    }

    /**
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return \Sma\Layout\Admin
     */
    public static function updateButtonNotifications(string $title = null, string $icon = null, string $label = null, string $labelStatus = null, string $footLinkUrl = null, string $footLinkLabel = null)
    {
        return Container::getJsonRequest()->updateButtonNotifications($title, $icon, $label, $labelStatus, $footLinkUrl, $footLinkLabel);
    }

    /**
     * @param string $id
     * @param string $url
     * @param string $data
     * @return \Sma\Layout\Admin
     */
    public static function addButtonNotificationsLink(string $id, string $url, string $label, string $icon = null, string $statusOrColor = null)
    {
        return Container::getJsonRequest()->addButtonNotificationsLink($id, $url, $label, $icon, $statusOrColor);
    }

    /**
     * @param string $title
     * @param string $icon
     * @param string $label
     * @param string $labelStatus
     * @param string $footLinkUrl
     * @param string $footLinkLabel
     * @return \Sma\Layout\Admin
     */
    public static function updateButtonAlerts(string $title = null, string $icon = null, string $label = null, string $labelStatus = null, string $footLinkUrl = null, string $footLinkLabel = null)
    {
        return Container::getJsonRequest()->updateButtonAlerts($title, $icon, $label, $labelStatus, $footLinkUrl, $footLinkLabel);
    }

    /**
     * @param string $id
     * @param string $url
     * @param string $data
     * @return \Sma\Layout\Admin
     */
    public static function addButtonAlertsLink(string $id, string $url, string $data)
    {
        return Container::getJsonRequest()->addButtonAlertsLink($id, $url, $data);
    }

    /**
     * @param string $username
     * @param string $imgsrc
     * @param string $imgalt
     * @param string $title
     * @param string $subtitle
     * @param string $body
     * @param string $footer
     * @return \Sma\Layout\Admin
     */
    public static function updateHeaderUser(string $username = null, string $imgsrc = null, string $imgalt = null, string $title = null, string $subtitle = null, string $body = null, string $footer = null)
    {
        return Container::getJsonRequest()->updateHeaderUser($username, $imgsrc, $imgalt, $title, $subtitle, $body, $footer);
    }

    /**
     * Titre et titre court généraux de l'application
     * @param string $title
     * @param string $shortTitle
     * @return \Sma\Layout\Admin
     */
    public static function updateHeaderTitle(string $title, string $shortTitle)
    {
        return Container::getJsonRequest()->updateHeaderTitle($title, $shortTitle);
    }

    /**
     * Clean all breadcrumb links
     * @return \Sma\Layout\Admin
     */
    public static function cleanHeaderUser()
    {
        return Container::getJsonRequest()->cleanHeaderUser();
    }

    public static function updateSettings()
    {
        return Container::getJsonRequest()->updateSettings();
    }

    /**
     * @param string $title
     * @param string $subtitle
     * @return \Sma\Layout\Admin
     */
    public static function setPageTitle(string $title, string $subtitle = '')
    {
        return Container::getJsonRequest()->setPageTitle($title, $subtitle);
    }

    /**
     * Clean all breadcrumb links
     * @return \Sma\Layout\Admin
     */
    public static function cleanBreadcrumb()
    {
        return Container::getJsonRequest()->cleanBreadcrumb();
    }

    /**
     * Add breadcrumb link
     * @param string $label
     * @param string $url
     * @return \Sma\Layout\Admin
     */
    public static function addBreadcrumbLink(string $label, string $url)
    {
        return Container::getJsonRequest()->addBreadcrumbLink($label, $url);
    }

    /**
     * Clean all breadcrumb links
     * @return \Sma\Layout\Admin
     */
    public static function cleanAlerts()
    {
        return Container::getJsonRequest()->cleanAlerts();
    }

    /**
     * Clean all breadcrumb links
     * @return \Sma\Layout\Admin
     */
    public static function cleanButtons()
    {
        return Container::getJsonRequest()->cleanButtons();
    }

    /**
     * Add an alert to the current page
     * @param string $title
     * @param string $message
     * @param string $status
     * @param bool $closable
     * @return \Sma\Layout\Admin
     */
    public static function addAlert(string $title = null, string $message = null, string $status = 'info', bool $closable = true)
    {
        return Container::getJsonRequest()->addAlert($title, $message, $status, $closable);
    }

    /**
     * @param string $message
     * @param string $title
     * @return \Sma\Layout\Admin
     */
    public static function info(string $message, string $title = null)
    {
        return Container::getJsonRequest()->info($message, $title);
    }

    /**
     * @param string $message
     * @param string $title
     * @return \Sma\Layout\Admin
     */
    public static function warning(string $message, string $title = null)
    {
        return Container::getJsonRequest()->warning($message, $title);
    }

    /**
     * @param string $message
     * @param string $title
     * @return \Sma\Layout\Admin
     */
    public static function error(string $message, string $title = null)
    {
        return Container::getJsonRequest()->error($message, $title);
    }

    /**
     * @param string $message
     * @param string $title
     * @return \Sma\Layout\Admin
     */
    public static function success(string $message, string $title = null)
    {
        return Container::getJsonRequest()->success($message, $title);
    }

    /**
     * Set HTML page content (body). If null, do nothing.
     * @param string $content
     * @return \Sma\Layout\Admin
     */
    public static function setPageContent($content)
    {
        return Container::getJsonRequest()->setPageContent($content);
    }

    /**
     * Clean common javascript
     * @return \Sma\Layout\Admin
     */
    public static function cleanScripts()
    {
        return Container::getJsonRequest()->cleanScripts();
    }

    /**
     * Append common javascript
     * @param string $script
     * @return \Sma\Layout\Admin
     */
    public static function appendScripts(string $script)
    {
        return Container::getJsonRequest()->appendScripts($script);
    }

    /**
     * Set HTML footer content (copyright)
     * @param string $content
     * @return \Sma\Layout\Admin
     */
    public static function setFooterContent(string $content)
    {
        return Container::getJsonRequest()->setFooterContent($content);
    }

    /**
     * @return \Sma\Layout\Admin
     */
    public static function cleanFooterLinks()
    {
        return Container::getJsonRequest()->cleanFooterLinks();
    }

    /**
     * @param string $label
     * @param string $url
     * @return \Sma\Layout\Admin
     */
    public static function addFooterLink(string $label, string $url)
    {
        return Container::getJsonRequest()->addFooterLink($label, $url);
    }

    /**
     * Si le body n'a pas changé, le renvoyer quand même
     * @param bool $trueOrFalse
     * @return \Sma\Layout\Admin
     */
    public static function forceRefreshBody($trueOrFalse = true)
    {
        return Container::getJsonRequest()->forceRefreshBody($trueOrFalse);
    }

    /**
     * Si les scripts n'ont pas changer, les envoyer quand même (exécuter quoi qu'il
     * arrive)
     * @param bool $trueOrFalse
     * @return \Sma\Layout\Admin
     */
    public static function forceRefreshScripts($trueOrFalse = true)
    {
        return Container::getJsonRequest()->forceRefreshScripts($trueOrFalse);
    }

    /**
     * Supprime le contenu de la page (body) dans la page courante ET la session
     * @return \Sma\Layout\Admin
     */
    public static function clearPageContent()
    {
        return Container::getJsonRequest()->clearPageContent();
    }

    /**
     * Render the JSON page
     * @return string
     */
    public static function render(int $renderType = null, bool $prettyPrintIfDev = true)
    {
        return Container::getJsonRequest()->render($renderType, $prettyPrintIfDev);
    }

    /**
     * Return to initial layout (from session or new one)
     * @return \Sma\Layout\Admin
     */
    public static function cancelUpdates()
    {
        return Container::getJsonRequest()->cancelUpdates();
    }

    /**
     * Set default render type
     * @param int $renderType
     * @return \Sma\Layout\Admin
     * @throws \Osf\Exception\ArchException
     */
    public static function setRenderType(int $renderType)
    {
        return Container::getJsonRequest()->setRenderType($renderType);
    }

    /**
     * Disable or enable layout in application dispatcher
     * @param type $disabled
     * @return \Sma\Layout\Admin
     */
    public static function setDisabled($disabled = true)
    {
        return Container::getJsonRequest()->setDisabled($disabled);
    }

    /**
     * Get sidebar menu
     * @return \Osf\Navigation\Item
     */
    public static function getMenu()
    {
        return Container::getJsonRequest()->getMenu();
    }

    /**
     * @staticvar bool $currentDevice
     * @param string $userAgent
     * @param string $httpHeaders
     * @return bool
     */
    public static function isMobile($userAgent = null, $httpHeaders = null)
    {
        return Container::getDevice()->isMobile($userAgent, $httpHeaders);
    }

    /**
     * @staticvar bool $currentDevice
     * @param string $userAgent
     * @param string $httpHeaders
     * @return bool
     */
    public static function isTablet($userAgent = null, $httpHeaders = null)
    {
        return Container::getDevice()->isTablet($userAgent, $httpHeaders);
    }

    /**
     * @return bool
     */
    public static function isMobileOrTablet()
    {
        return Container::getDevice()->isMobileOrTablet();
    }

}