<?php
namespace App\Common\Form;

use Osf\Form\Element\ElementAbstract;

/**
 * Paramétrage du formulaire de filtrage CRUD
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package common
 * @subpackage form
 */
class FormCrudFilterSettings
{
    protected $sortOptions = [];
    protected $hasDates = false;
    protected $fields = [];

    public function getSortOptions()
    {
        return $this->sortOptions;
    }
    
    /**
     * Option de tri
     * @param string $key
     * @param string $label
     * @param bool $asc
     * @return $this
     */
    public function addSortOption(string $key, string $label, bool $asc = true) 
    {
        if (!$this->sortOptions) {
            $this->sortOptions[''] = __("Trier par...");
        }
        $this->sortOptions[$key] = ($asc ? '▲' : '▼') . ' ' . $label;
        return $this;
    }
    
    /**
     * @param $hasDates bool
     * @return $this
     */
    public function setHasDates($hasDates = true)
    {
        $this->hasDates = (bool) $hasDates;
        return $this;
    }

    /**
     * @return bool
     */
    public function hasDates(): bool
    {
        return $this->hasDates;
    }
    
    /**
     * Nouveau champ
     * @param ElementAbstract $field
     * @return $this
     */
    public function addField(ElementAbstract $field)
    {
        $this->fields[] = $field;
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }
    
    /**
     * Nombre d'inputs du formulaire
     * @return int
     */
//    public function countInputs($displayKey)
//    {
//        $count = 1;
//        $count += $this->getSortOptions() ? 1 : 0;
//        $count += $this->hasDates() ? 2 : 0;
//        $count += count($this->getFields());
//        return $count;
//    }
}