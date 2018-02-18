<?php
namespace App\Ticket;

use Osf\Stream\Text;
use Sma\Controller\Json as JsonAction;
use Sma\Session\Identity;
use Sma\Log;
use App\Common\Container;
use H, DB, ACL, Exception;

/**
 * Espace administration
 *
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
        H::layout()->addBreadcrumbLink('tickets', H::url('ticket'));
    }
    
    public function indexAction()
    {
        $this->disableView();
        $this->dispatchUri(H::url('ticket', 'list'));
    }
    
    public function listAction()
    {
        if (Container::getDevice()->isMobile()) {
            $this->alertWarning('', __("La liste des tickets n'est pas optimisée pour les mobiles, utilisez une tablette ou un ordinateur pour plus de confort."));
        }
        H::layout()->setPageTitle(__("Tickets"))
                ->addBreadcrumbLink(__("liste"), H::url('ticket', 'list'));
        $form = new Form\FormFilter();
        $settings = $form->isPostedAndValid() ? $form->getValues() : [];
        $tickets = DB::getTicketWithPollsTable()->getTicketsForTable($settings);
        return ['tickets' => $tickets, 'formFilter' => $form];
    }
    
    public function addAction()
    {
        H::layout()->setPageTitle(__("Ouvrir un ticket"))
                ->addBreadcrumbLink(__("ajouter"), '');
        $form = new Form\FormTicket();
        if ($form->isPostedAndValid()) {
            DB::buildTicketRow()
                    ->populate($form->getValues())
                    ->setIdAccount(Identity::getIdAccount())
                    ->save();
            $this->alertSuccess(
                    __("Merci pour votre contribution !"),
                    __("Votre ticket a été proposé à l'équipe de développement."));
            $this->redirect(H::url('ticket', 'list'));
        }
        return ['form' => $form];
    }
    
    public function detailAction()
    {
        $privateEntries = ['closed', 'deleted'];
        $id = (int) $this->getParam('id');
        $ticket = DB::getTicketWithPollsTable()->find($id);
        if (!ACL::isAdmin() && (!$ticket 
                || in_array($ticket->getStatus(), $privateEntries)
                || ($ticket->getStatus() === 'draft' && $ticket->getIdAccount() !== Identity::getIdAccount()))) {
            $this->redirect(H::url('ticket', 'list'));
            return;
        }
        H::layout()->setPageTitle(__("Ticket #") . $id)
                ->addBreadcrumbLink(__("détail"), '');
        if (ACL::isAdmin()) {
            $formLog = new Form\FormLog();
            if ($formLog->isPostedAndValid()) {
                $comment = $formLog->getValue('log');
                DB::buildTicketLogRow()
                    ->setComment($comment)
                    ->setIdAccount(Identity::getIdAccount())
                    ->setIdTicket($id)
                    ->save();
                $formLog = new Form\FormLog();
            }
        }
        $log = DB::getTicketLogTable()->buildSelect(['id_ticket' => $id])->columns(['id', 'date_insert', 'comment'])->execute();
        return ['ticket' => $ticket, 'log' => $log, 'formLog' => isset($formLog) ? $formLog : null];
    }
    
    public function editAction()
    {
        $id = (int) $this->getParam('id');
        $ticket = DB::getTicketTable()->find($id);
        $form = DB::getTicketTable()->getForm()
                ->setTitle(__("Modifier un ticket"), 'edit')
                ->hydrate($ticket->toArray(), null, false, true);
        if ($form->isPostedAndValid()) {
            $ticket->setValues($form->getValues())->save();
            $this->redirect(H::url('ticket', 'list'));
        }
        return ['form' => $form];
    }
    
    public function pollAction()
    {
        $this->disableLayout();
        $id = filter_input(INPUT_POST, 'id');
        $st = filter_input(INPUT_POST, 'st');
        $result = 2;
        if (is_numeric($id) && in_array($st, [0, 1])) {
            try {
                $values = ['id_account' => Identity::getIdAccount(), 'id_ticket' => (int) $id];
                $exists = (bool) DB::getTicketPollTable()->find($values);
                if ($exists && !$st) {
                    DB::getTicketPollTable()->delete($values);
                }
                if (!$exists && $st) {
                    DB::getTicketPollTable()->insert($values);
                }
                $result = $st;
            } catch (Exception $e) {
                Log::error($e->getMessage(), 'DB', $e);
            }
        } else {
            Log::hack('Mauvaises valeurs de poll', [$id, $st, Identity::getIdAccount()]);
        }
        return ['result' => $result];
    }
    
    public function commentAction()
    {
        $this->disableLayout();
        $comment = trim(filter_input(INPUT_POST, 'comment'));
        if (strlen($comment) > 1 && $comment !== 'reset') {
            Log::info(Text::crop($comment, 255), 'COMMENT');
        }
        return ['comment' => $comment];
    }
}
