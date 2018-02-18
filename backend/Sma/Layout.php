<?php
namespace Sma;

use Osf\Stream\Json;
use Osf\Application\OsfApplication as Application;
use Osf\Helper\Tab;
use App\Common\Container;
use Sma\Plugin\LayoutPlugin;
use Osf\Exception\ArchException;

/**
 * Vue JS dynamic layout controller
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage layout
 */
abstract class Layout
{
    const RENDER_UPDATE = 0; // Envoie uniquement les changement
    const RENDER_INIT   = 1; // Envoie l'ensemble des données du layout
    const RENDER_AUTO   = 2; // Envoie d'abord l'ensemble des données, ensuite l'update
    
    const DO_WRITE   = 'w'; // Write new items and data
    const DO_APPEND  = 'a'; // Append items or data to existing
    const DO_UPDATE  = 'u'; // Update items or data
    const DO_DESTROY = 'd'; // Destroy designed items or data
    
    const DELETE_LEAF_VALUE = null;
    
    protected $layout = [];
    protected $layoutInitial = [];
    protected $renderType = self::RENDER_AUTO;
    
    /**
     * @return \Osf\Session\AppSession
     */
    protected function getSession()
    {
        return Container::getSession('jslayout');
    }
    
    public function __construct()
    {
        $session = $this->getSession();
        if ($session->get('layout')) {
            $this->layout = $session->layout;
        }
        $this->layoutInitial = $this->layout;
    }
    
    /**
     * Render the JSON page
     * @return string
     */
    public function render(int $renderType = null)
    {
        // Calcul des données à rendre
        $data = $this->getComputedRenderData($renderType);
        
        // Enregistrement de l'état en cours en session.
        // Etat modifié éventuellement dans la classe fille
        $this->registerLayout();
        
        // Récupération JSON des données à envoyer
        return Json::encode($data);
    }
    
    /**
     * Get data to send to frontend VueJS app
     * @param int $renderType
     * @return array
     */
    protected function getComputedRenderData($renderType): array
    {
        $data = [];
        if ($renderType === null) {
            $renderType = $this->renderType;
        }
        $onlineLayout = $this->getSession()->layout ?: [];
        if ($renderType === self::RENDER_AUTO) {
            $renderType = $onlineLayout === [] ? self::RENDER_INIT : self::RENDER_UPDATE;
        }
        if ($renderType === self::RENDER_UPDATE) {
            $uDiffs = Tab::arrayDiffReplaceRecursive($onlineLayout, $this->layout);
            self::cleanLists($uDiffs);
            $data[self::DO_UPDATE] = $uDiffs;
        } else {
            $data[self::DO_WRITE] = $this->layout;
        }
        return $data;
    }
    
    /**
     * To be surcharged in subclass to modify some elements in session
     * @return $this
     */
    protected function registerLayout()
    {
        $this->getSession()->layout = $this->layout;
        return $this;
    }
    
    /**
     * Return to initial layout (from session or new one)
     * @return $this
     */
    public function cancelUpdates()
    {
        $this->layout = $this->layoutInitial;
        return $this;
    }
    
    /**
     * Set default render type
     * @param int $renderType
     * @return $this
     * @throws \Osf\Exception\ArchException
     */
    public function setRenderType(int $renderType)
    {
        if (!in_array($renderType, [self::RENDER_AUTO, self::RENDER_INIT, self::RENDER_UPDATE])) {
            throw new ArchException('Unknown render type');
        }
        $this->renderType = $renderType;
        return $this;
    }
    
    /**
     * Delete null values of numeric keys items
     * @param array $list
     * @return $this
     */
    protected static function cleanLists(array &$list)
    {
        foreach ($list as $key => $value) {
            if (is_int($key) && $value === null) {
                unset($list[$key]);
            }
            if (is_array($value)) {
                self::cleanLists($list[$key]);
            }
        }
    }
    
    /**
     * Disable or enable layout in application dispatcher
     * @param type $disabled
     * @return $this
     */
    public function setDisabled($disabled = true)
    {
        Container::getApplication()->setDispatchStep(Application::RENDER_LAYOUT, !$disabled);
        return $this;
    }
    
    /**
     * Get sidebar menu
     * @return \Osf\Navigation\Item
     */
    public function getMenu()
    {
        return LayoutPlugin::loadMenu();
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->render();
    }
}
