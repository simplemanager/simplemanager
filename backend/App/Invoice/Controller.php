<?php
namespace App\Invoice;

use Osf\Exception\DisplayedException;
use Osf\Exception\ArchException;
use Osf\Stream\Text;
use Sma\Controller\Json as JsonAction;
use Sma\Bean\InvoiceBean as IB;
use Sma\Bean\ContactBean;
use Sma\Session\Identity;
use Sma\Pdf\Invoice;
use Sma\Log;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use App\Invoice\Model\Bean\InvoiceBeanHydrator;
use App\Recipient\Model\RecipientDbManager as RDM;
use App\Invoice\Form\FormInvoice;
use App\Common\Container;
use Exception;
use H, DB;

/**
 * Facturation
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
    const INVOICE = IB::TYPE_INVOICE;
    const ORDER   = IB::TYPE_ORDER;
    const QUOTE   = IB::TYPE_QUOTE;
    
    const TYPES = [self::QUOTE, self::ORDER, self::INVOICE];
    
    protected $type;
    protected $icon;
    protected $typeName;
    
    public function init()
    {
        $this->type = in_array($this->getParam('type'), self::TYPES) ? $this->getParam('type') : self::INVOICE;
        $this->typeName = IB::getTypeNameFromType($this->type);
        $this->icon = $this->type ? IB::getIconFromType($this->type) : null;
    }
    
    public function indexAction()
    {
        $this->dispatch(['controller' => 'invoice', 'action' => 'list']);
        //$this->layout()->setPageTitle(__("Facturation"));
    }
    
    public function editAction()
    {
        $refresh = false;
        
        // Aperçu
        if ($this->getParam('render')) {
            $this->disableView();
            $bean = Container::getSession()->get('invoicebean');
            $doc = new Invoice($bean);
            Container::getResponse()->setTypePdf();
            try {
                $doc->output();
            } catch (DisplayedException $e) {
                echo $e->getMessage();
            } catch (Exception $e) {
                echo __("La génération comporte des erreurs. Nous analysons la situation de notre côté.");
                Log::error($e->getMessage(), 'PDF', $e);
            }
        }

        // Formulaire
        else {
            
            // Si un destinataire doit être ajouté automatiquement, l'ajouter et 
            // récupérer son ID pour l'ajouter au input select des destinataires...
            $recipientValue = filter_input(INPUT_POST, 'recipient');
            if ($recipientValue && !is_numeric($recipientValue)) {
                $contact = RDM::parseString($recipientValue);
                if ($contact) {
                    $recipientId = RDM::addContact($contact);
                    $script = "$(document).ready(function(){addRecipientSelectOpt(" . $recipientId . ", '" . RDM::getTitle($contact) . "');});";
                    $this->layout()->appendScripts($script);
                }
            }
            
            
            // Détecte si c'est une modification ou un duplicata
            $id = $this->hasParam('id') ? (int) $this->getParam('id') : ($this->hasParam('from') ? (int) $this->getParam('from') : null);
            $bean = $id ? DB::getInvoiceTable()->getInvoiceBeanFromIdDocument($id) : null;
            
            // Conditions d'inaltérabilité
            if ($this->type === IB::TYPE_INVOICE && $bean instanceof IB) {
                if ($this->hasParam('id') && $bean->isInvoice() && $bean->getStatus() !== IB::STATUS_CREATED) {
                    $this->alertDanger(__("Action interdite"), __("Les conditions d'inaltérabilité vous interdisent de modifier une facture émise. Seules les factures à l'état 'brouillon' sont modifiables."));
                    $this->disableView();
                    H::layout()->clearPageContent()->forceRefreshBody(false);
                    return [];
                }
            }
            
            // Construction du formulaire
            $form = new FormInvoice($this->type, $this->hasParam('frsh'));
            $form->setDocumentId((int) $this->getParam('id'));
            
            // Met à jour la configuration des taxes
            if ($bean instanceof IB && Identity::hasTax() === $bean->getTaxFranchise()) {
                $bean->setTaxFranchise(!Identity::hasTax());
                $text = Identity::hasTax()
                        ? __("Attention: vous créez un document pour lequel vous aviez une franchise en base de TVA alors que vous êtes maintenant soumis à la TVA. Vos produits doivent être configurés avec un taux de TVA en vigueur pour que votre facture soit correcte.")
                        : __("Vous creez un document à partir d'une source qui comporte des produits taxés. Comme vous facturez avec franchise en base de TVA, cette facture se basera sur la valeur hors taxe de vos produits.");
                $this->alertWarning(__("Franchise en base de TVA"), $text);
            }
            
            $isCredit = $this->getParam('credit') === 'true';
            $params = $this->buildParams($bean, $this->hasParam('from'), $id, $form->isPosted(false), $isCredit);
            $form->setTitle($params['title'], $this->icon);
            
            // Définit le destinataire passé en paramètre
            if ($this->hasParam('recipient')) {
                $form->getElement('recipient')->setValue($this->getParam('recipient'));
            }
            
            // Valeur du input select des destinataires positionné sur le contact ajouté
            else if (isset($recipientId)) {
                $form->getElement('recipient')->setValue($recipientId);
            }
            
            // Si des valeurs sont postées, on les hydrate
            if ($form->isPosted()) {
                $refresh = true;
                $postedValues = $form->getPostedValues();
                $postedValues['recipient'] = isset($recipientId) ? $recipientId : $postedValues['recipient'];
                $form->hydrate($postedValues, null, true, true);
                
                // Creation d'un nouveau bean si tout est valide
                if ($form->isValid()) {
                    $action = $form->getElement('action')->getValue();
                    
                    $invoiceBean = (new InvoiceBeanHydrator())->hydrate($form)->getInvoiceBean();
                    
                    // Aperçu
                    if ($action === 'preview') {
                        Container::getSession()->set('invoicebean', $invoiceBean);
                        if (Identity::isLevelBeginner()) {
                            $this->alertWarning(__("N'oubliez pas d'enregistrer"), __("L'aperçu n'enregistre pas votre document. Cliquez sur le bouton 'enregistrer' une fois que votre document est complet. Vous pourrez ensuite l'envoyer au destinataire."));
                        }
                        $preview = true;
                    }

                    // Enregistrement
                    else {
                        if ($invoiceBean->getCodeAuto()) {
                            $invoiceBean->setCode(DB::getSequenceTable()->nextValue($form->getType()), true);
                        }
                        if ($bean) {
                            $invoiceBean->setIdInvoice($bean->getIdInvoice());
                        }
                        $invoiceBean->setId($params['update']);
                        $document = new Invoice($invoiceBean);
                        DB::getDocumentTable()->saveDocument($document->setDump($document->output(null, 'S')));
                        $this->redirect(H::url('invoice', 'list', ['type' => $this->type]));
                        $this->alertSuccess(sprintf(__("Document %s enregistré."), $invoiceBean->getCode()));
                    }
                }
            }
            
            // Populate du bean en base si c'est une modification 
            // et qu'il n'y a pas de valeurs postées
            else if ($bean) {
            
                // Si on crée un avoir, on coche la case avoir par défaut
                if ($bean instanceof IB && $bean->isInvoice() && $isCredit) {
                    $bean->setCredit(true);
                }
            
                $idDocumentLinked = isset($params['id_document_linked']) ? $params['id_document_linked'] : null;
                $form->setBean($bean, !$params['update'], $this->type, $idDocumentLinked);
            }
//            if (!array_key_exists('code', $_POST)) {
//                Container::getJsonRequest()->appendScripts("$(document).ready(function(){\$('#recipient')[0].selectize.focus()});");
//            }

            return [
                'form'    => $form, 
                'preview' => (isset($preview) ? $preview : false), 
                'type'    => $this->type, 
                'refresh' => $refresh
            ];
        }
    }
    
    /**
     * Paramètres du formulaire
     * @param InvoiceBean $bean
     * @param bool $from
     * @return array
     * @throws ArchException
     */
    protected function buildParams($bean, $from, $id, $formPosted, bool $credit)
    {
        if ($bean && !($bean instanceof IB)) {
            throw new ArchException('Bad bean type [' . get_class($bean) . ']');
        }
        $isExpert = Identity::isLevelExpert();
        $params = ['update' => null, 'linked_to' => null];
        $msgSuffix = __("Vérifiez les produits, leurs prix et réductions. Ce document se base sur vos paramètres, contacts et produits actuels, sauf pour les prix, les réductions et les descriptions que vous avez spécifié manuellement pour ce document.");
        switch (true) {
            
            // Modification d'un document existant
            case $bean && $bean->getType() === $this->type && !$from : 
                $params['title'] = sprintf(__("Modification de %s"), $bean->getCode());
                $params['update'] = $id;
                $msg = __("Des produits, contacts et paramètres ont pu être modifiés ou supprimés depuis la dernière mise à jour de ce document.");
                $formPosted || $isExpert || $this->alertInfo(sprintf(__("Modification de %s"), $this->typeName), $msg . ' ' . $msgSuffix);
                $formPosted || $isExpert || $this->alertAlreadySent($bean);
                break;
            
            // Clonage ou transformation dans un document différent
            case $bean && $bean->getType() === $this->type && $from : 
            case $bean && $bean->getType() !== $this->type :
                $typeName = $this->typeName . ($credit && $bean->getType() === self::INVOICE ? ' ' . __("d'avoir") : '');
                $params['title'] = ($this->type == self::QUOTE
                    ? sprintf(__("Nouveau %s basé sur %s"), Text::ucFirst($typeName), $bean->getCode()) 
                    : sprintf(__("Nouvelle %s basée sur %s"), Text::ucFirst($typeName), $bean->getCode()));
                $msg = __("Des produits, contacts et paramètres ont pu être modifiés ou supprimés depuis la création du document original.");
                $params['id_document_linked'] = $bean->getIdDocument();
                $formPosted || $isExpert || $this->alertInfo(sprintf(__("Création de %s depuis un document existant"), $typeName), $msg . ' ' . $msgSuffix);
                break;
            
            // Création
            default : 
                $params['title'] = ($this->type == self::QUOTE ? __("Nouveau") : __("Nouvelle")) 
                . ' ' . Text::ucFirst($this->typeName);
                break;
        }
        
        return $params;
    }
    
    public function previewAction() 
    {
        $docId = $this->getParam('view');
        if (!$docId) {
            $this->alertWarning(__("Requête invalide"));
            return [];
        }
        return ['doc' => $docId];
    }
    
    public function exportAction()
    {
        try {
            if (!is_numeric($this->getParam('dl'))) {
                throw new ArchException('Bad request');
            }
            $document = DB::getDocumentTable()->getDocument($this->getParam('dl'), null, true);
            $this->pdf($document['history']['dump']);
        } catch (\Exception $e) {
            $this->disableViewAndLayout();
            echo __("Document introuvable");
        }
    }
    
    // @task envoi des e-mails en différé si problème
    public function sendAction()
    {
        $this->layout()->setPageTitle(__("Envoi par e-mail"));
        $id = (int) $this->getParam('id');
        $doc = DB::getInvoiceTable()->select(['id_account' => Identity::getIdAccount(), 'id_document' => $id])->current();
        if (!($doc instanceof \Sma\Db\InvoiceRow)) {
            throw new ArchException('This is not an invoice row. Id: ' . $id);
        }
        $bean = $doc->getBeanUpToDate();
        $form = new Form\FormSendWithTpl($bean);
        $refresh = false;
        if ($form->isPosted()) {
            $refresh = true;
            if ($form->isPostedAndValid()) {
                DB::getLetterTemplateTable()->find($form->getValue('template'));
                try {
                    LTM::sendDocument(LTM::getTemplateBeanFromIdSafe($form->getValue('template')), $bean, $this);
                    $form = null;
                    $this->redirect(H::url('invoice', 'list', ['type' => $bean->getType()]));
                } catch (DisplayedException $e) {
                    $this->alertDanger(null, $e->getMessage());
                }
            }
        } else {
            if (!$this->alertBeforeSend($bean)) {
                $this->layout()->clearPageContent();
                $this->disableView();
                $form = null;
            }
        }
        return ['form' => $form, 'bean' => $bean, 'refresh' => $refresh];
    }
    
    /**
     * Lance des alertes et retoune false s'il ne faut pas continuer
     * @param IB $bean
     * @return bool
     */
    protected function alertBeforeSend(IB $bean): bool
    {
        if (!$bean->getRecipient()->getId()) {
            $this->alertDanger(__("Envoi impossible"), __("Vous n'avez pas spécifié de destinataire. Modifiez votre facture pour y ajouter un de vos clients."));
            return false;
        }
        if (!$bean->getRecipient()->getEmail()) {
            $this->alertDanger(__("Envoi impossible"), __("Le destinataire enregistré dans la facture n'a pas d'e-mail. Vous ne pouvez pas l'envoyer."));
            return false;
        }
        if ($bean->isInvoice() && $bean->hasWarning(true)) {
            if (in_array($bean->getStatus(), [$bean::STATUS_CANCELED, $bean::STATUS_CREATED])) {
                $this->alertWarning(__("Corrigez les alertes"), __("Nous ne pouvons pas encore envoyer votre facture. Celle-ci n'est malheureusement pas conforme au regard de la loi française. Vous devez corriger les problèmes signalés par des alertes et régénérer votre document pour qu'il soit complet."));
                return false;
            } else {
                $this->alertWarning(__("Document inconsistant ?"), __("Des erreurs ont été détectées dans cette facture qui semble déjà envoyée. Nous ne pouvons malheureusement pas les corriger à ce stade."));
            }
        }
        if ($bean->isInvoice() && $bean->getStatus() === IB::STATUS_CREATED && Identity::isLevelBeginner()) {
            $this->alertWarning(__("Règles d'inaltérabilité"), __("Une fois votre document envoyé, vous ne pourrez plus le modifier. La règlementation française vous interdit de modifier une facture émise. Vérifiez bien votre document avant de le transmettre à son destinataire."));
        }
        $canceledMsg = __("Ce document est marqué comme annulé. Êtes-vous sûr de vouloir l'envoyer ?");
        $this->alertAlreadySent($bean, $canceledMsg);
        return true;
    }
    
    /**
     * @param IB $bean
     * @param type $canceledMessage
     * @return void
     */
    protected function alertAlreadySent(IB $bean, $canceledMessage = null): void
    {
        if (!Identity::isLevelExpert()) {
            switch ($bean->getStatus()) {
                case IB::STATUS_CANCELED : 
                    if ($canceledMessage !== null) { 
                        $title = __("Document Annulé");
                        $this->alertWarning($title, $canceledMessage);
                    }
                    break;
                case IB::STATUS_PROCESSED : 
                case IB::STATUS_SENT :
                    $title = __("Document déjà envoyé ?");
                    $msg = sprintf(__("Votre document est marqué comme %s, il est possible que vous l'ayez déjà transmis à son destinataire."), Text::toLower($bean->getStatusName()));
                    $this->alertWarning($title, $msg);
                    break;
            }
        }
    }
    
    public function deleteAction()
    {
        $this->disableView();
        $type = $this->getParam('type');
        $id = $this->getParam('id');
        $tp = $this->getParam('tp') ?: 1;
        if (is_numeric($id)) {
            
            // Suppression du document
            if (!DB::getInvoiceTable()->deleteInvoiceFromIdDocument($id)) {
                $this->alertDanger(__("impossible de supprimer les données liées à ce document"));
            }
//            else if (!DB::getDocumentTable()->deleteSafe((int) $id)) {
//                $this->alertDanger(__("Impossible de supprimer ce document"));
//            }
            
            // Alerte
            else {
                $this->alertInfo('Document supprimé définitivement');
            }
        }
        $this->ajaxCall(H::url('invoice', 'list', ['type' => $type, 'tp' => $tp]), '#clist');
    }
    
    public function listAction()
    {
        //$this->layout()->setPageTitle(__("Facturation"));
        $type = $this->getParam('type');
        $type = in_array($type, self::TYPES) ? $type : self::INVOICE;
        $form = new Form\FormFilter($type);
        $form->isPostedAndValid();
        return [
            'data' => DB::getInvoiceTable()->getInvoices($type, $form->getValues()), 
            'type' => $type,
            'formFilter' => $form
        ];
    }
    
    public function viewAction()
    {
        $id = $this->getParam('id');
        $bean = null;
        if (is_numeric($id) && $id) {
            $bean = DB::getInvoiceTable()->getInvoiceBeanFromIdDocument($id);
        }
        $history = null;
//        if ($bean instanceof IB) {
//            $this->layout()->setPageTitle($bean->getTypeName(true) . ' ' . $bean->getCode());
//            $history = DB::getDocumentTable()->getDocument($bean->getIdDocument(), null, false, true);
//        }
        return ['bean' => $bean, 'history' => $history];
    }
    
    /**
     * Changement de statut
     */
    public function chstAction()
    {
        $hasRedirect = $this->hasParam(self::REDIRECT_AUTO_PARAM);
        $id = (int) filter_input(INPUT_POST, 'id') ?: $this->getParam('id');
        $status =   filter_input(INPUT_POST, 'st') ?: $this->getParam('st');
        $result = DB::getDocumentTable()->updateStatus($id, $status, 'status_' . $status, true, __("Changement manuel d'état"), true);
        $result = 1;
        if ($hasRedirect) {
            $this->disableView();
            if (!$result) {
                Log::error("Problème changement d'état", 'CHST', [$id, $result]);
                $this->alertWarning(
                        __("Problème lors du marquage"), 
                        __("Un problème a été rencontré lors de la modification d'état de votre document. Veuillez nous excuser pour la gène occasionnée. Nous nous penchons sur ce disfonctionnement."));
            }
            $this->redirectAuto();
        } else {
            $this->disableViewAndLayout();
            echo $result;
        }
    }
    
    /**
     * Régénération d'un document avec les nouvelles infos sur le destinataires, les produits, etc.
     */
    public function updateAction()
    {
        $this->disableView();
        try {
            $invoiceBean = DB::getInvoiceTable()->getInvoiceBeanFromIdDocument((int) $this->getParam('id'));
            if ($invoiceBean->getStatus() !== $invoiceBean::STATUS_CREATED) {
                throw new DisplayedException(__("Seuls les documents à l'état 'brouillon' peuvent être régénérés."));
            }
            $hasWarning = $invoiceBean->hasWarning(true);
            $invoiceBean
                    ->setDefaults()
                    ->setProvider(Identity::getContactBean())
                    ->setRecipient(DB::getContactTable()->getBean($invoiceBean->getRecipient()->getId()));
            $document = new Invoice($invoiceBean);
            DB::getDocumentTable()->saveDocument($document->setDump($document->output(null, 'S')), __("régénération"));
            $hasNewWarning = $invoiceBean->hasWarning(true);
            if (!Identity::isLevelExpert()) {
                if ($hasWarning && !$hasNewWarning) {
                    if ($invoiceBean->isInvoice()) {
                        $this->alertSuccess(__("Bravo !"), __("Votre facture est maintenant conforme ! Il est enfin possible de l'envoyer."));
                    } else {
                        $this->alertSuccess(__("Bravo !"), __("Vous n'avez plus d'alerte, votre document est complet."));
                    }
                } else if ($hasNewWarning) {
                    $this->alertWarning(null, __("Il vous reste encore une ou plusieurs alertes à résoudre."));
                }
                $this->alertInfo(sprintf(__("Document %s régénéré"), $invoiceBean->getCode()), __("Les modifications effectuées dans vos préférences et sur le destinataire du document ont été répercutées dans votre document. Nous vous conseillons de le visualiser avant de l'envoyer pour s'assurer que tout est correct."));
            }
            $this->redirectAuto(H::url('invoice', 'list', ['nr' => 1], ['type', 'ai', 'tp']));
        } catch (DisplayedException $e) {
            $this->alertDanger(__("Opération annulée"), $e->getMessage());
        } catch (Exception $e) {
            Log::error("Problème lors de la régénération d'un document", 'INV', $e);
            $this->alertDanger(__("Problème rencontré"), __("Une erreur a été détectée lors de la mise à jour de votre document. Nos équipes techniques se penchent sur le problème. Veuillez nous excuser pour la gêne occasionnée."));
        }
    }
    
    // Accès directs
    
    public function newinvoiceAction()
    {
        $this->redirect((string) H::url('invoice', 'edit', ['type' => self::INVOICE]));
    }
    
    public function neworderAction()
    {
        $this->redirect((string) H::url('invoice', 'edit', ['type' => self::ORDER]));
    }
    
    public function newquoteAction()
    {
        $this->redirect((string) H::url('invoice', 'edit', ['type' => self::QUOTE]));
    }
    
    public function invoicesAction()
    {
        $this->redirect((string) H::url('invoice', 'list', ['type' => self::INVOICE]));
    }
    
    public function ordersAction()
    {
        $this->redirect((string) H::url('invoice', 'list', ['type' => self::ORDER]));
    }
    
    public function quotesAction()
    {
        $this->redirect((string) H::url('invoice', 'list', ['type' => self::QUOTE]));
    }
}
