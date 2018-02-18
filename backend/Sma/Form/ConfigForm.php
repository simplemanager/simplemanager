<?php

namespace Sma\Form;

use Osf\Config\OsfConfig as Config;
use Sma\Controller\Json as JsonController;

/**
 * Générateur de formulaires avec configuration
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage form
 */
class ConfigForm
{
    /**
     * @var \Osf\Config\OsfConfig
     */
    protected $formConfig;
    
    /**
     * @var \Osf\Form\TableForm
     */
    protected $form;
    
    public function __construct(Config $formConfig)
    {
        $this->formConfig = $formConfig;
    }
    
    public function dispatch(JsonController $controller, bool $withDescriptions = true)
    {
        // Titres, icône
        $pageTitle = $this->getConfig('pageTitle');
        if ($pageTitle !== null) {
            $controller->pageTitle($pageTitle);
        }
        $this->form = $this->formConfig->getForm($withDescriptions);
        $title = $this->getConfig('title', null, '');
        $icon  = $this->getConfig('icon');
        if ($title || $icon) {
            $this->form->setTitle($title, $icon);
        }
        
        // Validation du formulaire
        if ($this->form->isPostedAndValid()) {
            $title   = $this->getConfig('alertSuccess', 'title');
            $content = $this->getConfig('alertSuccess', 'content');
            if ($title || $content) {
                $controller->alertSuccess($title, $content);
            }
            return true;
        }
        if (!$this->form->isPosted()) {
            $title   = $this->getConfig('alertInfo', 'title');
            $content = $this->getConfig('alertInfo', 'content');
            if ($title || $content) {
                $controller->alertInfo($title, $content);
            }
            return null;
        }
        return false;
    }
    
    public function getConfig(string $key, string $subKey = null, $valueIfNotFound = null)
    {
        static $config = null;
        
        if ($config === null) {
            $formConfig = $this->formConfig->getConfig();
            $config = isset($formConfig['config']) ? $formConfig['config'] : [];
        }
        if ($subKey === null) {
            return isset($config[$key]) ? $config[$key] : $valueIfNotFound;
        }
        return isset($config[$key][$subKey]) ? $config[$key][$subKey] : $valueIfNotFound;
    }
    
    /**
     * @return \Osf\Form\TableForm
     */
    public function getForm()
    {
        return $this->form;
    }
}