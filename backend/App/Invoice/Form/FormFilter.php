<?php
namespace App\Invoice\Form;

use App\Common\Form\FormCrudFilter;
use App\Common\Form\FormCrudFilterSettings;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSelect;
use Sma\Bean\InvoiceBean as IB;
use H;

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
    public function __construct(string $type)
    {
        $this->setAction(H::url('invoice', 'list', ['type' => $type]));
        $formSettings = new FormCrudFilterSettings();
        $formSettings->setHasDates()
            ->addSortOption('ca', __("Code (a-z)"))
            ->addSortOption('cd', __("Code (z-a)"), false)
            ->addSortOption('pa', __("Montant (0-n)"))
            ->addSortOption('pd', __("Montant (n-0)"), false)
            ->addSortOption('sa', __("Suivi (a-z)"))
            ->addSortOption('sd', __("Suivi (z-a)"), false)
//            ->addSortOption('da', __("Destinataire (a-z)"))
//            ->addSortOption('dd', __("Destinataire (z-a)"), false)
            ->addSortOption('dca', __("Date de création"))
            ->addSortOption('dcd', __("Date de création"), false)
            ->addSortOption('dua', __("Date de modification"))
            ->addSortOption('dud', __("Date de modification"), false);
        if ($this->displayAll()) {
            $formSettings->addField((new ElementInput('pi'))
                ->setTypeNumber()
                ->setPlaceholder(__("Montant min"))
                ->setAddonLeft(null, 'eur')
                ->setTooltip(__("Montant Minimum"))
                //->getHelper()->setAttribute('step', '0.01', true)->getElement()
                )
            ->addField((new ElementInput('pa'))
                ->setTypeNumber()
                ->setPlaceholder(__("Montant max"))
                ->setAddonLeft(null, 'eur')
                ->setTooltip(__("Montant Maximum"))
                //->getHelper()->setAttribute('step', '0.01', true)->getElement()
                );
        }
        if ($this->displayEssential()) {
            $formSettings->addField((new ElementSelect('st'))
                ->setAddonLeft(null, 'circle-o')
                ->setTooltip(__("État du document (suivi)"))
                ->setOptions(IB::getStatusNames($type, __("-- État --"))));
        }
        parent::__construct($formSettings);
    }
}