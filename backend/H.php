<?php

use Sma\View\Generated\StaticGeneratedViewHelper;
use App\Common\Container;

/**
 * Global access to view helpers with static methods
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package Sma
 * @subpackage H
 */
abstract class H extends StaticGeneratedViewHelper
{
    protected static function init()
    {
        self::setViewHelper(Container::getViewHelper());
    }
    
    /**
     * @return \Common\View\Helper\AddBox
     */
    public static function addBox($title, $data, $class = null)
    {
        return self::callHelper(__METHOD__, [$title, $data, $class]);
    }
    
    /**
     * @return \Common\View\Helper\AddLink
     */
    public static function addLink($link, $linksTitle = null)
    {
        return self::callHelper(__METHOD__, [$link, $linksTitle]);
    }
    
    /**
     * @return \Common\View\Helper\SidebarMenu
     */
    public static function sidebarMenu(Navigation $menu)
    {
        return self::callHelper(__METHOD__, [$menu]);
    }
    
    /**
     * @return \Common\View\Helper\AdminLayout
     */
    public static function adminLayout()
    {
        return self::callHelper(__METHOD__, []);
    }
    
    /**
     * @return \Sma\Layout\Admin
     */
    public static function layout(): \Sma\Layout\Admin
    {
        return Container::getJsonRequest();
    }
    
    /**
     * @return \Sma\View\Helper\Crud
     */
    public static function crud(\Sma\View\Helper\Crud\CrudConfig $config): \Sma\View\Helper\Crud
    {
        return Container::getCrud()($config);
    }
    
    public static function htmlCached(?string $key, $content, $elt = null, array $attributes = array(), $escape = true)
    {
        static $data = [];
        
        $key = $key ?? $content . '_' . $elt;
        $cacheKey = 'html:' . Container::getLocale()->getLangKey() . ':' . $key;
        
        if (isset($data[$key])) {
            return $data[$key];
        }
        
        $html = C::get($cacheKey);
        if (!$html) {
            $html = (string) self::html($content, $elt, $attributes, $escape);
            C::set($cacheKey, $html);
            $data[$key] = $html;
        }
        
        return $html;
    }
    
    /**
     * @return string
     */
    public static function iconCached($icon = null, $status = null, $iconColor = null, bool $fixWidth = false): string
    {
        static $icons = [];
        
        if ($icon === null || $icon === '') {
            return '';
        }
        $key = 'icon:' . $icon . '_' . $status . '_' . $iconColor . '_' . (int) $fixWidth;
        if (isset($icons[$key]))  {
            return $icons[$key];
        }
        $htmlIcon = C::get($key);
        if (!$htmlIcon) {
            $iconHelper = self::icon($icon, $status, $iconColor);
            $fixWidth && $iconHelper->addCssClass('icofix');
            $htmlIcon = (string) $iconHelper;
            C::set($key, $htmlIcon);
            $icons[$key] = $htmlIcon;
        }
        return $htmlIcon;
    }
}
