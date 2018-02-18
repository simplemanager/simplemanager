<?php
namespace App\Product\Form;

use App\Common\Form\FormCrudFilter;
use App\Common\Form\FormCrudFilterSettings;
use Osf\Form\Element\ElementInput;

/**
 * Filtrage et recherche dans la liste
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage product
 */
class FormFilter extends FormCrudFilter
{
    public function __construct()
    {
        $formSettings = new FormCrudFilterSettings();
        $formSettings->addSortOption('ca', __("Code (a-z)"))
            ->addSortOption('cd', __("Code (z-a)"), false)
            ->addSortOption('pa', __("Prix (0-n)"))
            ->addSortOption('pd', __("Prix (n-0)"), false)
            ->addSortOption('dca', __("Date d'ajout"))
            ->addSortOption('dcd', __("Date d'ajout"), false)
            ->addSortOption('dua', __("Date de modification"))
            ->addSortOption('dud', __("Date de modification"), false);
        if ($this->displayAll()) {
            $formSettings->setHasDates()
                ->addField((new ElementInput('pi'))
                    ->setTypeNumber()
                    ->setPlaceholder(__("Prix min"))
                    ->setAddonLeft(null, 'eur')
                    ->setTooltip(__("Prix Minimum"))
                    //->getHelper()->setAttribute('step', '0.01', true)->getElement()
                    )
                ->addField((new ElementInput('pa'))
                    ->setTypeNumber()
                    ->setPlaceholder(__("Prix max"))
                    ->setAddonLeft(null, 'eur')
                    ->setTooltip(__("Prix Maximum"))
                    //->getHelper()->setAttribute('step', '0.01', true)->getElement()
                    );
        }
        parent::__construct($formSettings);
    }
}