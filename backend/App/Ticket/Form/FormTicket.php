<?php
namespace App\Ticket\Form;

use Osf\Form\TableForm;
use Osf\Form\Element\ElementSubmit;
use DB;

/**
 * Log form
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package common
 * @subpackage forms
 */
class FormTicket extends TableForm
{
    public function init()
    {
        $this->setTitle(__("Nouveau ticket"), 'ticket')
                ->setTable(DB::getTicketTable())
                ->onlyFields(['category', 'title', 'content'])
                ->build();
        
        $this->setStarsForRequired(false);
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Proposer")));
    }
}
