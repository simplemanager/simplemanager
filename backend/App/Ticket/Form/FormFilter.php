<?php
namespace App\Ticket\Form;

use App\Common\Form\FormCrudFilter;
use App\Common\Form\FormCrudFilterSettings;
use Osf\Form\Element\ElementSelect;
use ACL;

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
    public static function getProgressionOptions()
    {
        $prOptions = [
            '' => __("-- Progression --"),
            'draft'  => __("Brouillon"),
            'under_study'  => __("En étude"),
            'accepted'  => __("Accepté"),
            'in_progress'  => __("En travaux"),
            'test'  => __("En test"),
            'beta'  => __("V. Beta"),
            'published'  => __("Réalisé"),
            'refused'  => __("Annulé")
        ];
        if (ACL::isAdmin()) {
            $prOptions['closed'] = __("Fermé");
            $prOptions['deleted'] = __("Supprimé");
        }
        return $prOptions;
    }
    
    public static function getTypeOptions()
    {
        return [
            '' => __("-- Type --"),
            'bug' => __("Bug"),
            'doc' => __("Question"),
            'improvement' => __("Amélioration"),
            'feature' => __("Nouveauté"),
        ];
    }
    
    public function __construct()
    {
        $formSettings = new FormCrudFilterSettings();
        if ($this->displayAll()) {
            $formSettings->setHasDates();
        }
        $formSettings->addSortOption('d', __("Par défaut"))
                ->addSortOption('ca', __("Type (a-z)"), false)
                ->addSortOption('cd', __("Type (z-a)"), false)
                ->addSortOption('pa', __("Progression (a-z)"))
                ->addSortOption('pd', __("Progression (z-a)"), false)
                ->addSortOption('va', __("Votes (0-n)"))
                ->addSortOption('vd', __("Votes (n-0)"), false)
                ->addSortOption('dca', __("Création"))
                ->addSortOption('dcd', __("Création"), false)
                ->addSortOption('dua', __("Mise à jour"))
                ->addSortOption('dud', __("Mise à jour"), false);
        if ($this->displayEssential()) {
            $formSettings->addField((new ElementSelect('ty'))
                        ->setAddonLeft(null, 'circle-o')
                        ->setTooltip(__("Type de ticket"))
                        ->setOptions(self::getTypeOptions()))
                ->addField((new ElementSelect('pr'))
                        ->setAddonLeft(null, 'circle-o')
                        ->setTooltip(__("Progression"))
                        ->setOptions(self::getProgressionOptions())
                        );
        }
        parent::__construct($formSettings);
    }
}