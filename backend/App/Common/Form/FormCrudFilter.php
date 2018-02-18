<?php
namespace App\Common\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Form\Element\ElementSelect;
use Osf\Validator\Validator as V;
use App\Common\Container;
use App\Common\Form\FormCrudFilterSettings as Settings;
use Sma\Session\Identity;

/**
 * Recherche et filtrage d'une liste
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package common
 * @subpackage forms
 */
class FormCrudFilter extends Form
{
    const DISPLAY_NOTHING     = 'no';
    const DISPLAY_SEARCH_SORT = 'rt';
    const DISPLAY_ESSENTIAL   = 'normal';
    const DISPLAY_ALL         = 'all';
    
    const SIZES = [
        1 => [8, 4],
        2 => [4, 4],
        3 => [3, 3],
        4 => [2, 7],
        5 => [2, 2],
        6 => [31, 61],
        7 => [31, 31]
    ];
    
    /**
     * @var Settings
     */
    protected $settings;
    
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
        $this->setHighlightFilledElts(true);
        parent::__construct(true);
    }
    
    /**
     * @param string $key
     * @return string
     * @throws ArchException
     */
    protected function getDisplay(string $key)
    {
        $userDisplay = Identity::getParam('interface', 'lform');
        switch ($key) {
            case self::DISPLAY_NOTHING : 
                return $userDisplay === self::DISPLAY_NOTHING;
            case self::DISPLAY_SEARCH_SORT : 
                return $userDisplay !== self::DISPLAY_NOTHING;
            case self::DISPLAY_ESSENTIAL : 
                return $userDisplay === self::DISPLAY_ESSENTIAL || $userDisplay === self::DISPLAY_ALL;
            case self::DISPLAY_ALL : 
                return $userDisplay === self::DISPLAY_ALL;
            default : 
                throw new ArchException('Bad display key [' . $key . ']');
        }
    }
    
    protected function displaySearchSort()
    {
        return $this->getDisplay(self::DISPLAY_SEARCH_SORT);
    }
    
    protected function displayEssential()
    {
        return $this->getDisplay(self::DISPLAY_ESSENTIAL);
    }
    
    protected function displayAll()
    {
        return $this->getDisplay(self::DISPLAY_ALL);
    }
    
    public function init()
    {
        if ($this->getDisplay(self::DISPLAY_NOTHING)) {
            return;
        }
        
        if (Container::getDevice()->isMobile()) {
            $this->setTitle(__("Filtres & Tris"), 'bars');
        }
        Container::getDevice()->isMobile() ? $this->setExpandable() : $this->setCollapsable();
        
        if ($this->displaySearchSort()) {
            $this->add((new ElementInput('q'))
                    ->setPlaceholder(__("Rechercher"))
                    ->setTypeSearch()
                    ->add(V::newStringLength(0, 250))
                    ->setAddonLeft(null, 'search'));

            $sortOptions = $this->settings->getSortOptions();
            if ($sortOptions) {
                $this->add((new ElementSelect('s'))
                        ->setAddonLeft(null, 'sort-amount-asc')
                        ->setOptions($sortOptions));
            }
        }

        if ($this->settings->hasDates() && $this->displayEssential()) {
            $this->add((new ElementInput('f'))
                    ->setTypeDate()
                    ->setPlaceholder(_("Du"))
                    ->setTooltip(__("Mis à jour<br />après cette date"), null, true)
                    ->setAddonLeft(null, 'calendar-minus-o'));

            $this->add((new ElementInput('t'))
                    ->setTypeDate()
                    ->setPlaceholder(_("Au"))
                    ->setAddonLeft(null, 'calendar-plus-o')
                    ->setTooltip(__("Mis à jour<br />avant cette date"), null, true));
        }
        
        foreach ($this->settings->getFields() as $field) {
            $this->add($field);
        }
        
        $size = self::SIZES[count($this->getElements())];
        
        /* @var $elt \Osf\Form\Element\ElementAbstract */
        foreach ($this->getElements() as $key => $elt) {
            $elt->getHelper()->setSize($size[$key === 'q' ? 1 : 0]);
            if ($elt->getValue()) {
                $elt->getHelper()->addCssClass('text-success');
            }
        }
        
        $this->add((new ElementSubmit('go'))->setValue(__("Filtrer / Chercher"))
                ->getHelper()->setSize($size[0])->addCssClasses(['btn-block'])->getElement());
    }
    
    public function isValid($values = null)
    {
        if (!Container::getDevice()->isMobileOrTablet()) {
            Container::getJsonRequest()->appendScripts("\$('#q').select().focus();");
        }
        return parent::isValid($values);
    }
    
    public function hasValue()
    {
        foreach ($this->getElements() as $elt) {
            if ($elt instanceof ElementSubmit) {
                continue;
            }
            if ($elt->getValue() !== '') {
                return true;
            }
        }
        return false;
    }
}
