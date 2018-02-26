<?php
namespace App\Document;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Osf\Db\Table\AbstractTableGateway as ATG;
use Osf\Exception\DisplayedException;
use Osf\Exception\ArchException;
use Osf\Helper\Tab;
use Sma\Bean\LetterTemplateBean as LTB;
use Sma\Controller\Json as JsonAction;
use Sma\Db\DocumentHistoryCurrentRow;
use Sma\Pdf\Letter as TcpdfLetter;
use Sma\Bean\InvoiceBean as IB;
use Sma\Bean\NotificationBean;
use Sma\Db\DocumentHistoryRow;
use Sma\Bean\ContactBean;
use Sma\Session\Identity;
use Sma\Bean\LetterBean;
use Sma\Db\DbRegistry;
use Sma\Log;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use App\Guest\Controller as GuestController;
use App\Document\Form\FormTemplate;
use App\Document\Form\FormLetter;
use App\Common\Container;
use Exception, H, DB, ACL;

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
//    public function init()
//    {
//        H::layout()->setPageTitle(__("Lettres & Documents"));
//        H::layout()->addBreadcrumbLink('courrier', H::url('document'));
//    }
    
    public function letterAction()
    {
        // Execute les preview, download...
        $result = $this->executePreviewActions();
        if ($result !== false) {
            return $result; 
        }
        
        $form = new FormLetter($this->hasParam('frsh'));

        // Détecte si c'est une modification ou un duplicata
        H::layout()->setPageTitle($this->hasParam('id') ? __("Modification") : ($this->hasParam('from') ? __("Duplicata") : __("Nouvelle Lettre")));
        $id = $this->hasParam('id') ? (int) $this->getParam('id') : ($this->hasParam('from') ? (int) $this->getParam('from') : null);

        // Populate du bean en base si c'est une modification
        $id && $form->setBean(DB::getDocumentTable()->getBean($id));
//            else if ($doc = Container::getSession()->get('letterform')) {
//                $form->hydrate($doc, null, true, true);
//            }                 

        // Surcharge du destinataire s'il est spécifié
        if ($this->getParam('recipient')) {
            $form->getElement('recipient')->setValue($this->getParam('recipient'));
        }

        // Enregistrement ou prévisualisation (post)
        if ($form->isPostedAndValid()) {
            $action = $form->getElement('action')->getValue();
            $letterBean = (new LetterBean())->populate($form->getValues());
            $this->hasParam('id') && $letterBean->setId((int) $this->getParam('id'));

            // Prévisualisation : passage du letterbean via la session
            if ($action === 'preview') {
                Container::getSession()->set('letterbean', $letterBean);
                $preview = true;
            }

            // Enregistrement : génération et envoi en base + redirection
            else {
                $letter = new TcpdfLetter($letterBean);
                DB::getDocumentTable()->saveDocument($letter->setDump($letter->generate()));
                $this->redirect(H::url('document'));
                $this->alertSuccess('Lettre enregistrée');
            }
        }

        return [
            'form' => $form, 
            'preview' => (isset($preview) ? $preview : false),
            'posted' => $form->isPosted()
        ];
    }
    
    protected function executePreviewActions()
    {
        // Génération du rendu
        if ($this->getParam('render')) {
            $this->disableView();
            if ($letterBean = Container::getSession()->get('letterbean')) {
                Container::getSession()->clean('letterbean');
                $this->getResponse()->setTypePdf();
                (new TcpdfLetter($letterBean))->generate(false);
            } else {
                $this->pdf(Container::getSession()->get('letterstream'));
                Container::getSession()->clean('letterstream');
            }
            return [];
        }
        
        // Contexte
        $templateCtx = $this->getRequest()->getAction() === 'template';
        
        // Aperçu d'un document existant
        if ($this->getParam('view')) {
            if ($templateCtx) {
                $tpl = DB::getLetterTemplateTable()->find($this->getParam('view'));
                $dataBean   = LTM::buildDataTypeBean($tpl['data_type'], true);
                $recipient  = LTM::getDataTypeBeanRecipient($dataBean);
                $letterBean = LTM::render(unserialize($tpl['bean']), $dataBean);
                $letterBean
                        ->setProvider(ContactBean::buildContactBeanFromContactId())
                        ->setRecipient($recipient);
                Container::getSession()->set('letterbean', $letterBean);
            } else {
                $document = DB::getDocumentTable()->getDocument($this->getParam('view'), null, true);
                $stream = $document['history']['dump'];
                Container::getSession()->set('letterstream', $stream);
            }
            return ['preview' => true, 'posted' => $this->getParam('detail')];
        }
        
        // Download direct
        if ($this->getParam('dl')) {
            try {
                if ($templateCtx) {
                    $this->todo();
                } else {
                    $document = DB::getDocumentTable()->getDocument($this->getParam('dl'), null, true);
                    $this->pdf($document['history']['dump']);
                }
            } catch (\Exception $e) {
                $this->disableViewAndLayout();
                echo __("Document introuvable");
            }
            return [];
        }
        
        return false;
    }
    
    public function indexAction()
    {
        $form = new Form\FormFilter();
        $form->isPostedAndValid();
        return [
            'data' => DB::getDocumentTable()->getDocuments('letter', $form->getValues()),
            'formFilter' => $form
        ];
    }
    
    public function sendAction()
    {
        $this->disableLayout();
        $id = (int) $this->getParam('id');
        try {
            $bean = DB::getDocumentTable()->getBean($id);
            if (!($bean instanceof LetterBean)) {
                throw new ArchException('This is not an invoice row. Id: ' . $id);
            }
            $mail = $bean->buildEmail();
            if (!$mail->send()) {
                $this->alertDanger(
                        __("Problème lors de l'envoi"), 
                        __("Un disfonctionnement a été constaté lors de l'envoi de l'e-mail. Nous nous penchons sur ce problème, veuillez nous excuser pour la gêne occasionnée."));
                Log::error("Erreur lors de l'envoi d'un e-mail.", 'MAIL', $mail);
            } else if (!DB::getDocumentTable()->updateStatus($id, $bean::STATUS_SENT, $bean::EVENT_SENDING)) {
                $this->alertWarning(
                        __("Mise à jour de l'état impossible"), 
                        __("Votre e-mail a bien été envoyé mais le changement d'état de votre message en 'envoyé' n'a pas fonctionné. Nous nous penchons sur ce problème, veuillez nous excuser pour la gêne occasionnée."));
                Log::error("Erreur lors du changement de statut d'un document.", 'DB', ['id' => $id]);
            } else {
                $this->alertSuccess(__("Email envoyé"));
            }
        } catch (DisplayedException $e) {
            $this->alertWarning(__("Envoi impossible"), $e->getMessage());
        } catch (\Exception $e) {
            Log::error($e->getMessage(), 'MAIL', $e);
            $this->alertDanger(__("Problème rencontré"), __("Un problème à été rencontré au moment de la construction de l'e-mail ou de son envoi. Nous nous penchons sur le problème. Veuillez nous excuser pour la gêne occasionnée."));
        }
        $this->dispatchUri(H::url('document', 'index', ['ai' => $this->getParam('ai'), 'tp' => (int) $this->getParam('tp')]));
        return [];
    }
    
    public function templateAction()
    {
        // Accès à la fonctionnalité
        if (!$this->hasAccessToTemplates()) {
            return [];
        }
        
        // Download, preview...
        $result = $this->executePreviewActions();
        if ($result !== false) {
            return $result;
        }
        
        // Création et hydratation du formulaire
        $form = $this->hydrateFormTemplate(new Form\FormTemplate());

        // Enregistrement ou prévisualisation (post)
        if ($form && $form->isPostedAndValid()) {
            $action = $form->getElement('action')->getValue();
            // $this->hasParam('id') && $letterBean->setId((int) $this->getParam('id'));

            // Prévisualisation : passage du letterbean via la session
            if ($action === 'preview') {
                $dataType   = $form->getElement('data_type')->getValue();
                $dataBean   = LTM::buildDataTypeBean($dataType, true);
                $recipient  = LTM::getDataTypeBeanRecipient($dataBean);
                $letterBean = $this->twigProcess($recipient, $dataBean, $form);
                if ($letterBean) {
                    Container::getSession()->set('letterbean', $letterBean);
                    $preview = true;
                }
            }

            // Enregistrement
            else {
                $values = $form->getValuesForTemplate();
                DB::getLetterTemplateTable()->saveTemplate($values, $this->getParam('id'));
                $this->redirect(H::url('document', 'templates'));
                $this->alertSuccess('Modèle enregistré');
            }
        }

        return [
            'form' => $form, 
            'preview' => (isset($preview) ? $preview : false),
            'posted' => $form && $form->isPosted()
        ];
    }
    
    /**
     * Hydrate le form template
     * @param FormTemplate $form
     * @return FormTemplate
     */
    protected function hydrateFormTemplate(FormTemplate $form): ?FormTemplate
    {
        // Titre de page en fonction du contexte
        H::layout()->setPageTitle($this->hasParam('id') 
                ? __("Modification") 
                : ($this->hasParam('from') 
                        ? __("Duplicata") 
                        : ($this->hasParam('fromletter') 
                                ? __("Depuis une lettre") 
                                : __("Nouveau Modèle"))));
        
        // Récupération de l'id 
        $id = $this->hasParam('id') 
                ? (int) $this->getParam('id') 
                : ($this->hasParam('from') 
                        ? (int) $this->getParam('from') 
                        : ($this->hasParam('fromletter') 
                            ? (int) $this->getParam('fromletter') 
                            : null));
        
        // Hydratation du formulaire
        if ($id) {
            $values = $this->hasParam('id') 
                    ? DB::getLetterTemplateTable()->findSafe($id)
                    : ($this->hasParam('from')
                            ? DB::getLetterTemplateTable()->find($id)
                            : DB::getDocumentTable()->findSafe($id));
            if (!$values || !($this->hasParam('from') || $values['category'] === 'mine' || ACL::isAdmin())) {
                $this->alertWarning(__("Vous ne pouvez pas modifier ce modèle"));
                return null;
            }
            $values = $values->toArray();
            if (isset($values['data_type_filters']) && $values['data_type_filters']) {
                $values['data_type_filters'] = explode(',', $values['data_type_filters']);
            }
            $fields = $this->hasParam('formletter') 
                    ? ['title', 'description']
                    : FormTemplate::TEMPLATE_SPECIFIC_FIELDS;
            $bean = $this->hasParam('fromletter')
                    ? DB::getDocumentTable()->getBean($id)
                    : unserialize($values['bean']);
            $form->setBean($bean, Tab::reduce($values, $fields));
        }
        
        return $form;
    }
    
    protected function twigProcess($recipient, $dataBean, $form)
    {
        try {
            $letterBean = LTM::render($form->buildTemplateBean($recipient), $dataBean);
            return $letterBean;
        } catch (\Twig_Error_Syntax $e) {
            $msg = preg_replace('/ in "[a-f0-9]+"/', '', $e->getMessage());
            $this->alertDanger(
                    __("Erreur de syntaxe détectée"), 
                    sprintf(__("L'interpréteur à renvoyé les informations suivantes : %s L'erreur se trouve dans le ou les champs entourés en rouge ci-dessous."), $msg));
            if ($form->getElement('object')->getValue() !== '') {
                $form->getElement('object')->addError(__("Erreur de syntaxe potentielle (ligne 1)"));
            }
            $form->getElement('body')->addError(__("Erreur de syntaxe potentielle (lignes 2 à n)"));
        } catch (\Twig_Error $e) {
            $this->alertDanger(
                    __("Erreur de compilation"),
                    __("Une erreur est survenue lors de la compilation de votre modèle. Veuillez vérifier la syntaxe et la cohérence de vos données."));
            Log::error($e->getMessage(), 'TWIG', $e->getTraceAsString());
        } catch (\Exception $e) {
            $this->alertDanger(__("Erreur détectée"), __("Impossible d'interpréter votre modèle. Notre équipe de développement a été notifiée du problème. Veuillez nous excuser pour la gêne occasionnée."));
            Log::error($e->getMessage(), 'UNKNOWN', $e->getTraceAsString());
        }
        return false;
    }
    
    public function templatesAction()
    {
        if (!$this->hasAccessToTemplates()) {
            return [];
        }
        
        $form = new Form\FormFilter(true);
        $settings = $form->isPostedAndValid() ? $form->getValues() : [];
        return [
            'data' => DB::getLetterTemplateTable()->getTemplates($settings),
            'formFilter' => $form
        ];
    }
    
    /**
     * Détail d'un template
     */
    public function tpldetailAction()
    {
        if (!$this->hasAccessToTemplates()) {
            return [];
        }
        
        return ['tpl' => DB::getLetterTemplateTable()->getTemplateForRead($this->getParam('id'))];
    }
    
    protected function hasAccessToTemplates()
    {
        if (!LTM::isActive()) {
            $this->disableView();
            $this->layout()->clearPageContent();
            $this->alertWarning(
                    __("Fonctionnalité à activer"), 
                    __("Vous devez activer les modèles de lettre dans 'Mes paramètres' -> 'Fonctionnalités optionnelles' afin de pouvoir accéder à cette page.")
                );
            return false;
        }
        return true;
    }
    
    public function historyAction()
    {
//        H::layout()->setPageTitle(__("Historique"));
        $this->todo();
    }
    
    public function deleteAction()
    {
        $this->deleteDocument(DB::getDocumentTable(), 'index');
    }
    
    public function deletetplAction()
    {
        $this->deleteDocument(DB::getLetterTemplateTable(), 'templates');
    }
    
    protected function deleteDocument(ATG $table, string $action)
    {
        $this->disableView();
        $id = $this->getParam('id');
        $tp = $this->getParam('tp') ?: 1;
        if (is_numeric($id)) {
            if (!$table->deleteSafe((int) $id)) {
                $this->alertDanger(__("Vous ne pouvez pas supprimer ce document"));
            } else if (!Identity::isLevelExpert()) {
                $this->alertInfo('Document supprimé');
            }
        }
        $this->ajaxCall(H::url('document', $action, ['tp' => $tp]), '#clist');
    }
    
    public function viewAction()
    {
        try {
            $doc = DB::getDocumentTable()->getDocument($this->getParam('id'), null, false, true);
            return ['doc' => $doc];
        } catch (Exception $e) {
            Log::hack('Tentative de récupération du document [' . $this->getParam('id') . ']', $e);
            $this->redirect(H::url('document'));
        }
    }
    
    public function proceduresAction()
    {
        $this->disableView();
        H::layout()->setPageTitle(__("Démarches administratives"));
        echo H::msg('Démarches administratives (à venir)');
    }
    
    public function procedureAction()
    {
        $this->disableView();
        echo H::msg('Moteur de démarche administrative (à venir)');
    }
    
    public function exportAction()
    {
        if (!is_numeric($this->getParam('dh'))) {
            Log::hack('Tentative de recuperation avec un id non numérique', $this->getParams());
            $this->notFound();
            return;
        }
        $hDocId = (int) $this->getParam('dh');
        $idAccount = Identity::getIdAccount();
        $where = ['id_account' => $idAccount, 'id' => $hDocId];
        $row = DB::getDocumentHistoryTable()->select($where)->current();
        if (!$row || !($row instanceof DocumentHistoryRow)) {
            Log::hack('Tentative de recuperation de document par ID (loggué)', $this->getParams());
            $this->notFound(); 
            return;
        }
        $this->pdf($row->getDump());
    }
    
    // TODO : prévenir si c'est un document obsolète
    public function dlAction()
    {
        // Récupération du document
        $key = $this->getParam('k');
        $dispositionInline = $this->getParam('d') != 'dl';
        $where = ['hash' => $key];
        $row = DB::getDocumentHistoryCurrentTable()->select($where)->current();
        if (!$row || !($row instanceof DocumentHistoryCurrentRow)) {
            Log::hack('Tentative de recuperation de document par HASH', $this->getParams());
            $this->notFound(); 
            return;
        }
        
        // Autologin s'il y a une clé correspondant au destinataire
        if (!Identity::isLogged() && !GuestController::isLogged() && !GuestController::login($this->getParam('r'))) {
            throw new DisplayedException(__("L'accès à ce document requiert une authentification."));
        }
        
        // Changement du statut en "lu" si c'est le destinataire qui effectue l'action et que le statut est "envoyé"
        if (GuestController::isLogged() && $row->getStatus() === IB::STATUS_SENT) {
            DbRegistry::markGuestDocumentRead(GuestController::getContactBean(), $row->getIdDocument(), $row->getType());
            $typeName = $row->getType() === BDB::TYPE_LETTER ? __("lettre") : IB::getTypeNameFromType($row->getType());
            $notification = (new NotificationBean())
                        ->setContent(sprintf(__("Votre contact %s à lu votre %s '%s'."), 
                                GuestController::getContactBean()->getComputedTitle(),
                                $typeName,
                                $row->getSubject()))
                        ->setLink(H::baseUrl(H::url('document', 'dl', ['k' => $row->getHash()]), true))
                        ->setIcon('info')->setColor('blue');
            DbRegistry::notificationPush($notification, $row->getIdAccount(), false);
        }
        
        // Vérifications
        if (!Identity::isLogged() && !in_array($row->getStatus(), [IB::STATUS_SENT, IB::STATUS_READ, IB::STATUS_PROCESSED])) {
            throw new DisplayedException(__("Ce document n'est accessible que par son propriétaire."));
        }
        
        // Affichage
        $this->pdf($row->getDump(), $dispositionInline, $row->getBean()->buildFileName());
    }
    
    /**
     * Génération de documents
     */
    public function generateAction()
    {
        $this->pageTitle(__("Création"), __("depuis un modèle"));
        $tplId = (int) $this->getParam('tpl');
        $template = DB::getLetterTemplateTable()->find($tplId);
        if ($template->getDataType() !== LTB::DT_RECIPENT) {
            $this->alertWarning(__("Modèle lié à un document"), __("Allez dans les sections 'devis', 'commandes' ou 'factures' pour envoyer le ou les documents avec votre modèle."));
            $this->disableView();
            return;
        }
        $form = new Form\FormGenerator($template);
        $preview = false;
        if ($form->isPostedAndValid()) {

            // Prévisualisation : passage du letterbean via la session
            $action = $form->getElement('action')->getValue();
            if ($action === 'preview') {
                $letterBean = $template->render((int) $form->getValue('target')[0]);
                if ($letterBean) {
                    Container::getSession()->set('letterbean', $letterBean);
                    $preview = true;
                }
            }

            // Enregistrement
            else {
                $cpt = 0;
                foreach ($form->getValue('target') as $target) {
                    $letterBean = $template->render($target);
                    $letter = new TcpdfLetter($letterBean);
                    DB::getDocumentTable()->saveDocument($letter->setDump($letter->generate()));
                    $this->redirect(H::url('document'));
                    $cpt++;
                }
                $this->alertSuccess(sprintf(__("%d document(s) créé(s)"), $cpt));
            }
        }
        return ['form' => $form, 'preview' => $preview];
    }
    
    /**
     * Changement de statut
     */
    public function chstAction()
    {
        $this->disableViewAndLayout();
        $id = (int) filter_input(INPUT_POST, 'id');
        $status = filter_input(INPUT_POST, 'st');
        if (!in_array($status, [BDB::STATUS_CREATED, BDB::STATUS_CANCELED, BDB::STATUS_PROCESSED, BDB::STATUS_SENT])) {
            Log::hack('Bad status [' . $status . ']');
            echo 0;
            return;
        }
        echo DB::getDocumentTable()->updateStatus($id, $status, 'status_' . $status, true, null, true);
    }
}
