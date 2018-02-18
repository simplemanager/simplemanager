<?php
namespace App\Board;

use Sma\Controller\Json as JsonAction;
use DB, DateTime;

/**
 * Tableau de Bord
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 16 nov. 2013
 * @package company
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    public function init()
    {
        $this->layout()->setPageTitle(__("Résultats"));
    }
    
    public function indexAction()
    {
        $form = new Form\FormBoardFilter(null, null, $this->getParam('range') === 'year');
        $posted = $form->isPostedAndValid();
        $from = DateTime::createFromFormat('d/m/Y', $form->getValue('from'));
        $to = DateTime::createFromFormat('d/m/Y', $form->getValue('to'));
        $stats = DB::getInvoiceTable()->getStats($from, $to);
        return ['stats' => $stats, 'form' => $form, 'posted' => $posted];
    }
}
