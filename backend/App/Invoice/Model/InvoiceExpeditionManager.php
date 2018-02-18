<?php
namespace App\Invoice\Model;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Sma\Session\Identity;
use Sma\Bean\InvoiceBean;
use Sma\Cache as SC;
use Sma\Mail;
use App\Invoice\Form\FormSend;
use App\Invoice\Controller;
use App\Common\Container;
use DB;

/**
 * Gestion des expéditions de factures
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage invoice
 */
class InvoiceExpeditionManager
{
    /**
     * @param FormSend $form
     * @param InvoiceBean $bean
     * @param Controller $ctrl
     * @return boolean
     * @deprecated
     */
    public static function buildAndSendDocument(FormSend $form, InvoiceBean $bean, Controller $ctrl)
    {
        $sent = false;
        try {
            self::sendDocumentByEmail($form->getValue('subject'), $form->getValue('body'), $bean);
            $sent = true;
        } catch (\Exception $e) {
            Log::error("Erreur d'envoi d'un mail client !", 'MAIL', $e->getMessage());
            $ctrl->alertDanger(__("Envoi impossible"), __("Malheureusement nous n'avons pas pu envoyer votre e-mail. Nous travaillons sur ce problème. Veuillez nous excuser pour la gêne occasionnée."));
        }
        if ($sent) {
            $ctrl->alertSuccess(__("Message envoyé"), __("L'e-mail a été envoyé au destinataire et votre document est maintenant défini comme 'Envoyé'."));
            $bean->setStatus(InvoiceBean::STATUS_SENT);
            Container::getCacheSma()->cleanItem(SC::C_DOCUMENT, $bean->getIdDocument());
            DB::getInvoiceTable()->find($bean->getIdInvoice())
                ->setBean($bean)
                ->setStatus(InvoiceBean::STATUS_SENT)
                ->save();
            DB::getDocumentTable()->updateStatus($bean->getIdDocument(), BDB::STATUS_SENT, BDB::EVENT_SENDING);
        }
        return $sent;
    }
    
    protected static function sendDocumentByEmail(string $subject, string $body, InvoiceBean $bean): bool
    {
        $document = DB::getDocumentTable()->getDocument($bean->getIdDocument(), null, true);
        $fileName = $bean->buildFileName();
        $bodyHtml = Container::getMarkdown()->text(htmlspecialchars($body));
        
        return (new Mail())
            ->setSubject($subject)
            ->addText($bodyHtml, false)
            ->addFrom($bean->getProvider()->getEmail(), $bean->getProvider()->getComputedTitle())
            ->addTo($bean->getRecipient()->getEmail(), $bean->getRecipient()->getComputedTitle())
            ->addAttachment($document['history']['dump'], Mail::MIMETYPE_PDF, $fileName)
            ->setFooterUser()
            ->send();
    }
}
