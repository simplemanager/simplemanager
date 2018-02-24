<?php
namespace App\Recipient;

use Osf\Exception\DisplayedException;
use Sma\Controller\Json as JsonAction;
use Sma\Session\Identity as I;
use Sma\Log;
use App\Recipient\Model\RecipientDbManager as DM;
use App\Recipient\Form\FormRecipient;
use App\Common\Container;
use H, L, DB;

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
    const CONTACT_SETTINGS = 'ctsettings';
    
    public function init()
    {
        //$this->layout()->setPageTitle(__("Clients & Contacts"));
    }
    
    public function indexAction()
    {
        $this->redirect(H::url('recipient', 'list'));
    }
    
    /**
     * Liste des contacts
     * @return array
     */
    public function listAction()
    {
        $form = new Form\FormFilter();
        $form->isPostedAndValid();
        return ['data' => DM::getContactsForTable($form->getValues()), 'formFilter' => $form];
    }
    
    /**
     * Ajout et édition d'un contact
     * @return array
     */
    public function editAction()
    {
        $form = (new FormRecipient(false, $this->getParam('f') ?? 'firstname'));
        if ($id = $this->getParam('id')) {
            $form->hydrate(DM::getContactForForm($id), null, true, true);
        }
        if ($form->isPostedAndValid()) {
            $formValues = $form->getValues();
            $newId = $id ? DM::updateContact($formValues, $id)->getId() : DM::addContact($formValues);
            if ($newId) {
                if ($form->isInModal()) {
                    if ($this->getParam('from') === 'inv') {
                        $js = "addRecipientSelectOpt(" . $newId . ",'" . DM::getTitle($formValues) . "');";
                    } else if ($this->hasParam('tp') || !$id) {
                        $tp = $this->getParam('tp') ?: 1;
                        $js = "$.ajaxCall('" . H::url('recipient', 'list', ['tp' => $tp]) . "', '#clist');";
                    } else {
                        $js = "$.ajaxCall('" . H::url('recipient', 'view', ['id' => $id, 'frsh' => 1]) . "');";
                    }
                    L::appendScripts("$('#mform').modal('hide');$('body').removeClass('modal-open');$('.modal-backdrop').remove();" . $js);
                } else {
                    $this->redirectAuto(H::url('recipient', 'list', [], ['tp']));
                }
            } else {
                $this->alertDanger(__("Une erreur a été détectée lors de l'enregistrement de votre contact."), __("Ce problème a été envoyé à nos équipes qui font tout leur possible pour le régler. Toutes nos excuses pour la gêne occasionnée."));
            }
        }
        return ['form' => $form];
    }
    
    /**
     * Détail d'un contact
     * @return array
     */
    public function viewAction()
    {
        $withDecorations = $this->getParam('for') !== 'modal';
        $withDecorations || $this->disableLayout();
        $bean = DB::getCompanyTable()->getContactBean($this->getParam('id'));
        return ['bean' => $bean, 'decorations' => $withDecorations];
    }
    
    /**
     * Suppression
     */
    public function deleteAction()
    {
        $this->disableView();
        $id = $this->getParam('id');
        $tp = $this->getParam('tp') ?: 1;
        try {
            DM::deleteRecipient($id);
            $this->alertSuccess(__("Contact supprimé"));
            if (!I::isLevelExpert()) {
                $this->alertWarning(__("A propos de la suppression"), __("Veuillez noter que votre contact supprimé peut être lié à un ou plusieurs documents, qui contiennent toujours des informations relatives à ce contact. Si vous voulez modifier ou copier un document contenant un destinataire supprimé, il sera nécessaire de leur attribuer un nouveau contact."));
            }
            $this->ajaxCall(H::url('recipient', 'list', ['tp' => $tp]), '#clist');
        } catch (DisplayedException $e) {
            Container::getJsonRequest()->clearPageContent();
            $this->alertDanger(__("Suppression impossible"), $e->getMessage());
        } catch (\Exception $e) {
            Container::getJsonRequest()->clearPageContent();
            $this->alertDanger(__("Suppression impossible"), __("Un problème est survenu lors de la suppression. Nous nous penchons sur le problème. Veuillez nous excuser pour la gêne occasionnée."));
            Log::error(__("Problème lors de la suppression d'un contact"), 'DB', $e);
        }
    }
    
    /**
     * Export des utilisateurs avec filtrage
     * @task Factoriser avec product::exportAction
     */
    public function exportAction()
    {
        $this->export(DB::getContactTable(), __("Mes contacts"), (new Form\FormFilter())->getValues());
    }
    
    public function importAction()
    {
        $this->todo();
    }
}
