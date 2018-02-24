<?php
namespace App\Recipient\Form;

use App\Common\Form\FormCrudFilter;
use App\Common\Form\FormCrudFilterSettings;

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
        if ($this->displayAll()) {
            $formSettings->setHasDates();
        }
        $formSettings->addSortOption('na', __("Nom (a-z)"))
            ->addSortOption('nd', __("Nom (z-a)"), false)
            ->addSortOption('ca', __("Contact (a-z)"))
            ->addSortOption('cd', __("Contact (z-a)"), false)
            ->addSortOption('ea', __("E-mail (a-z)"))
            ->addSortOption('ed', __("E-mail (z-a)"), false)
            ->addSortOption('dca', __("Date d'ajout"))
            ->addSortOption('dcd', __("Date d'ajout"), false)
            ->addSortOption('dua', __("Date de modification"))
            ->addSortOption('dud', __("Date de modification"), false);
        parent::__construct($formSettings);
    }
}
