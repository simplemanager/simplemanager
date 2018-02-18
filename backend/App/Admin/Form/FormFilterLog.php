<?php
namespace App\Admin\Form;

use App\Common\Form\FormCrudFilter;
use App\Common\Form\FormCrudFilterSettings;
// use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSelect;
use DB, H;

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
class FormFilterLog extends FormCrudFilter
{
    public function __construct()
    {
        $this->setAction(H::url('admin', 'logs'));
        $formSettings = new FormCrudFilterSettings();
//        $formSettings->addSortOption('ca', __("Code (a-z)"))
//            ->addSortOption('cd', __("Code (z-a)"), false)
//            ->addSortOption('pa', __("Prix (0-n)"))
//            ->addSortOption('pd', __("Prix (n-0)"), false)
//            ->addSortOption('dca', __("Date d'ajout"))
//            ->addSortOption('dcd', __("Date d'ajout"), false)
//            ->addSortOption('dua', __("Date de modification"))
//            ->addSortOption('dud', __("Date de modification"), false);
        $categories = DB::getLogTable()->execute('SELECT DISTINCT category FROM log ORDER BY category ASC');
        $categTab = ['' => '-- CATEGORY --'];
        foreach ($categories as $category) {
            $categTab[(string) $category['category']] = (string) $category['category'];
        }
        
        // @task [DB] Autocomplétion sur les comptes : ici et dans admin -> notifications
        $accounts = DB::getAccountTable()->buildSelect()->columns(['id', 'firstname', 'lastname'])->execute();
        $accountTab = ['' => '-- ACCOUNT --'];
        foreach ($accounts as $account) {
            $accountTab[$account['id']] = '#' . $account['id'] . ' ' . $account['firstname'] . ' ' . $account['lastname'];
        }
        $formSettings->setHasDates()
            ->addField((new ElementSelect('level'))
                    ->setPlaceholder(__("Niveau"))
                    ->addOption('info', 'Info')
                    ->addOption('warning', 'Warning')
                    ->addOption('error', 'Error')
                    ->setMultiple()
                    ->setValue([])
                )
            ->addField((new ElementSelect('category'))
                    ->setOptions($categTab)
                )
            ->addField((new ElementSelect('account'))
                    ->setOptions($accountTab)
                );
        parent::__construct($formSettings);
    }
}
