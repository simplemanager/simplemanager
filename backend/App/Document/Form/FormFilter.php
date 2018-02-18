<?php
namespace App\Document\Form;

use Osf\Form\Element\ElementSelect;
use App\Common\Form\FormCrudFilter;
use App\Common\Form\FormCrudFilterSettings;
use Sma\Bean\LetterBean as LB;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;

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
    public function __construct(bool $forFormTemplate = false)
    {
        $formSettings = $forFormTemplate 
                ? $this->getTemplateSettings() 
                : $this->getLetterSettings();
        parent::__construct($formSettings);
    }
    
    protected function getLetterSettings()
    {
        $formSettings = new FormCrudFilterSettings();
        $formSettings->setHasDates()
            ->addSortOption('oa', __("Objet (a-z)"))
            ->addSortOption('od', __("Objet (z-a)"), false)
            ->addSortOption('da', __("Destinataire (a-z)"))
            ->addSortOption('dd', __("Destinataire (z-a)"), false)
            ->addSortOption('dca', __("Date de création"))
            ->addSortOption('dcd', __("Date de création"), false)
            ->addSortOption('dua', __("Date de modification"))
            ->addSortOption('dud', __("Date de modification"), false);
        if ($this->displayEssential()) {
            $formSettings->addField((new ElementSelect('st'))
                ->setAddonLeft(null, 'circle-o')
                ->setTooltip(__("État du document (suivi)"))
                ->setOptions(LB::getStatusNames(LB::TYPE_LETTER, __("-- État --"))));
        }
        return $formSettings;
    }
    
    protected function getTemplateSettings()
    {
        $formSettings = new FormCrudFilterSettings();
        $formSettings
            ->addSortOption('ta', __("Titre (a-z)"))
            ->addSortOption('td', __("Titre (z-a)"), false)
//            ->addSortOption('oa', __("Objet (a-z)"))
//            ->addSortOption('od', __("Objet (z-a)"), false)
//            ->addSortOption('dca', __("Date de création"))
//            ->addSortOption('dcd', __("Date de création"), false)
            ->addSortOption('dua', __("Mise à jour"))
            ->addSortOption('dud', __("Mise à jour"), false);
        if ($this->displayEssential()) {
            $formSettings->addField((new ElementSelect('dt'))
                ->setAddonLeft(null, 'dot-circle-o')
                ->setTooltip(__("Données liées"))
                ->setOptions(LTM::getDataTypeOptions()));
            $formSettings->addField((new ElementSelect('ca'))
                ->setAddonLeft(null, 'cubes')
                ->setTooltip(__("Type de modèle"))
                ->setOptions(LTM::getCategoryOptions()));
        }
        if ($this->displayAll()) {
            $formSettings->setHasDates();
        }
        return $formSettings;
    }
}