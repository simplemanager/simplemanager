<?php
namespace Sma\Db\DbRegistry;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Osf\Exception\ArchException;
use Sma\Db\DocumentHistoryCurrentRow;
use Sma\Bean\DocumentBeanInterface;
use Sma\Bean\LetterBean;
use Sma\Bean\GuestBean;
use Sma\Bean\ContactBean;
use Sma\Bean\InvoiceBean as IB;
use App\Common\Container as C;
use Sma\Cache as SC;
use Sma\Log;
use DB;

/**
 * Requêtes liées à l'environnement invité
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait GuestManagement
{
    /**
     * @param ContactBean $contact
     * @return GuestBean
     */
    public static function getGuestBean(ContactBean $contact): GuestBean
    {
        $bean = new GuestBean();
        self::buildGuestBeanInvoices($contact, $bean);
        self::buildGuestBeanLetters($contact, $bean);
        return $bean;
    }
    
    /**
     * Statistiques liées aux documents invoices
     * @param ContactBean $contact
     * @param GuestBean $bean
     * @return void
     * @throws ArchException
     */
    protected static function buildGuestBeanInvoices(ContactBean $contact, GuestBean $bean): void
    {
        $sql = 'SELECT total_ht, total_ttc, status, type FROM invoice_document '
                . 'WHERE id_recipient=? AND status IN (\'sent\', \'read\', \'processed\')';
        $params = [$contact->getId()];
        $rows = DB::getInvoiceTable()->prepare($sql)->execute($params);
        foreach ($rows as $row) {
            switch (true) {
                case $row['type'] === IB::TYPE_INVOICE && in_array($row['status'], [IB::STATUS_SENT, IB::STATUS_READ]) && $row['total_ht'] >= 0 : 
                    $bean->setInvoicesToPayCount($bean->getInvoicesToPayCount() + 1);
                    $bean->setInvoicesToPayAmountHt($bean->getInvoicesToPayAmountHt() + $row['total_ht']);
                    $bean->setInvoicesToPayAmountTtc($bean->getInvoicesToPayAmountTtc() + $row['total_ttc']);
                    break;
                case $row['type'] === IB::TYPE_INVOICE && in_array($row['status'], [IB::STATUS_SENT, IB::STATUS_READ]) && $row['total_ht'] < 0 :
                    $bean->setCreditsToPayCount($bean->getCreditsToPayCount() + 1);
                    $bean->setCreditsToPayAmountHt($bean->getCreditsToPayAmountHt() - $row['total_ht']);
                    $bean->setCreditsToPayAmountTtc($bean->getCreditsToPayAmountTtc() - $row['total_ttc']);
                    break;
                case $row['type'] === IB::TYPE_INVOICE && $row['status'] === IB::STATUS_PROCESSED && $row['total_ht'] >= 0 :
                    $bean->setInvoicesPayedCount($bean->getInvoicesPayedCount() + 1);
                    $bean->setInvoicesPayedAmountHt($bean->getInvoicesPayedAmountHt() + $row['total_ht']);
                    $bean->setInvoicesPayedAmountTtc($bean->getInvoicesPayedAmountTtc() + $row['total_ttc']);
                    break;
                case $row['type'] === IB::TYPE_INVOICE && $row['status'] === IB::STATUS_PROCESSED && $row['total_ht'] < 0 :
                    $bean->setCreditsPayedCount($bean->getCreditsPayedCount() + 1);
                    $bean->setCreditsPayedAmountHt($bean->getCreditsPayedAmountHt() - $row['total_ht']);
                    $bean->setCreditsPayedAmountTtc($bean->getCreditsPayedAmountTtc() - $row['total_ttc']);
                    break;
                case $row['type'] === IB::TYPE_ORDER && in_array($row['status'], [IB::STATUS_SENT, IB::STATUS_READ]) :
                    $bean->setOrdersToSign($bean->getOrdersToSign() + 1);
                    break;
                case $row['type'] === IB::TYPE_ORDER && $row['status'] === IB::STATUS_PROCESSED :
                    $bean->setOrdersSigned($bean->getOrdersSigned() + 1);
                    break;
                case $row['type'] === IB::TYPE_QUOTE && $row['status'] === IB::STATUS_SENT :
                    $bean->setQuotesToConsult($bean->getQuotesToConsult() + 1);
                    break;
                case $row['type'] === IB::TYPE_QUOTE && in_array($row['status'], [IB::STATUS_READ, IB::STATUS_PROCESSED]) :
                    $bean->setQuotesConsulted($bean->getQuotesConsulted() + 1);
                    break;
                default : 
                    throw new ArchException('Inconsistant row ?');
            }
        }
    }
    
    /**
     * Statistiques liées aux lettres
     * @param ContactBean $contact
     * @param GuestBean $bean
     * @return void
     * @throws ArchException
     */
    protected static function buildGuestBeanLetters(ContactBean $contact, GuestBean $bean): void
    {
        $sql = 'SELECT count(id) as cpt, status '
                . 'FROM document '
                . 'WHERE type=\'letter\' AND id_recipient = ? AND status IN (\'sent\', \'processed\') '
                . 'GROUP BY status';
        $rows = DB::getDocumentTable()->prepare($sql)->execute([$contact->getId()]);
        foreach ($rows as $row) {
            if (in_array($row['status'], [IB::STATUS_READ, IB::STATUS_PROCESSED])) {
                $bean->setLetterRead((int) $row['cpt']);
            } else if ($row['status'] === IB::STATUS_SENT) {
                $bean->setLetterToRead((int) $row['cpt']);
            } else {
                throw new ArchException('Unknown status [' . $row['status'] . ']');
            }
        }
    }
    
    /**
     * Liste des factures d'un destinataire donné
     * @param ContactBean $contact
     * @param string $type
     * @return mixed
     * @throws ArchException
     */
    public static function getGuestInvoices(ContactBean $contact, string $type = IB::TYPE_INVOICE)
    {
        if (!in_array($type, IB::TYPES)) {
            throw new ArchException('Bad invoice type [' . $type . ']');
        }
        $sql = 'SELECT invoice_document.id_invoice as id, invoice_document.status, invoice_document.code, '
                . 'invoice_document.total_ttc, invoice_document.bean, document_history_current.hash '
                . 'FROM invoice_document, document_history_current '
                . 'WHERE invoice_document.id_document = document_history_current.id_document '
                . 'AND invoice_document.id_recipient=? '
                . 'AND invoice_document.status IN (\'sent\', \'read\', \'processed\') '
                . 'AND invoice_document.type = ? '
                . 'ORDER BY invoice_document.code DESC';
        $params = [$contact->getId(), $type];
        return DB::getInvoiceTable()->prepare($sql)->execute($params);
    }
    
    /**
     * Liste des lettres d'un destinataire donné
     * @param ContactBean $contact
     * @return mixed
     */
    public static function getGuestLetters(ContactBean $contact)
    {
        $sql = 'SELECT dhc.id_document as id, dhc.id_document_history as idh, dhc.subject, dhc.status, MIN(de.date) as date '
                . 'FROM document_history_current as dhc, document_event as de '
                . 'WHERE dhc.id_document=de.id_document '
                . 'AND dhc.id_recipient = ? '
                . 'AND dhc.status IN (\'sent\', \'read\', \'processed\') '
                . 'AND dhc.type = \'letter\' '
                . 'AND de.event IN (\'sending\', \'status_sent\') '
                . 'GROUP BY dhc.id_document, dhc.id_document_history, dhc.subject, dhc.status '
                . 'ORDER BY date DESC';
        $params = [$contact->getId()];
        return DB::getDocumentHistoryCurrentTable()->prepare($sql)->execute($params);
    }
    
    /**
     * Marquer comme lu
     * @param ContactBean $contact
     * @param int $idDocument
     * @param string $type
     * @param string|null $comment
     * @throws ArchException
     */
    public static function markGuestDocumentRead(ContactBean $contact, int $idDocument, string $type, ?string $comment = null): void
    {
        self::markGuestDocument($contact, $idDocument, $type, $comment, IB::STATUS_READ);
    }
    
    /**
     * Marquer comme signé ou payé
     * @param ContactBean $contact
     * @param int $idDocument
     * @param string $type
     * @param string|null $comment
     * @throws ArchException
     */
    public static function markGuestDocumentProcessed(ContactBean $contact, int $idDocument, string $type, ?string $comment = null): void
    {
        if (!in_array($type, [IB::TYPE_INVOICE, IB::TYPE_ORDER])) {
            Log::hack("Tentative de changement de statut d'un document de type [" . $type . ']', [$contact->getEmail(), $idDocument, $type, $comment]);
            throw new ArchException('Tentative interdite de changement de statut');
        }
        self::markGuestDocument($contact, $idDocument, $type, $comment, IB::STATUS_PROCESSED);
    }
    
    /**
     * Marquage d'un document par Guest
     * @param ContactBean $contact
     * @param int $idDocument
     * @param string $type
     * @param string|null $comment
     * @throws ArchException
     */
    protected static function markGuestDocument(ContactBean $contact, int $idDocument, string $type, ?string $comment, string $status): void
    {
        $where = ['id_recipient' => $contact->getId(), 'id_document' => $idDocument, 'type' => $type];
        $doc = DB::getDocumentHistoryCurrentTable()->buildSelect($where)->execute()->current();
        if (!$doc || !($doc instanceof DocumentHistoryCurrentRow)) {
            Log::error('Document introuvable', 'DB', [$contact->getId(), $idDocument, $type]);
            throw new ArchException('Document introuvable');
        }
        $events = [
            BDB::STATUS_READ => BDB::EVENT_READ,
            BDB::STATUS_PROCESSED => BDB::EVENT_PROCESS,
        ];
        if (!isset($events[$status])) {
            throw new ArchException('Guest cannot mark document to status [' . $status . ']');
        }
        DB::getDocumentTable()->updateStatus($doc->getIdDocument(), $status, $events[$status], false, $comment);
        // C::getCacheSma()->cleanItem(SC::C_DOCUMENT, $doc->getIdDocument(), $doc->getIdAccount());
        C::getCacheSma()->cleanUserCache($doc->getIdAccount());
    }
    
    /**
     * Retourne le bean du document si le contact est le destinataire
     * @param ContactBean $contact
     * @param int $idh
     * @return DocumentBeanInterface|null
     */
    protected static function getGuestDocumentHistory(ContactBean $contact, int $idh): ?DocumentBeanInterface
    {
        $row = DB::getDocumentHistoryTable()->find($idh);
        $source = $row->getSource();
        if (!$source) {
            Log::hack('Document introuvable (tentative de recherche de document inexistant ?)', [$contact->getId(), $idh]);
            return null;
        }
        $bean = unserialize($source);
        if (!($bean instanceof DocumentBeanInterface)) {
            Log::hack('Type de document illisible', [$contact->getId(), $idh]);
            return null;
        }
        if ($bean->getRecipient()->getId() !== $contact->getId()) {
            Log::hack('Accès interdit au document (tentative de lecture de document ?)', [$contact->getId(), $idh]);
            return null;
        }
        return $bean;
    }
    
    /**
     * Recherche d'un document dont le destinataire est le contact
     * @param ContactBean $contact
     * @param int $idh
     * @return LetterBean|null
     */
    public static function getGuestLetterBean(ContactBean $contact, int $idh): ?LetterBean
    {
        $bean = self::getGuestDocumentHistory($contact, $idh);
        if (!($bean instanceof LetterBean)) {
            Log::hack("Ce document n'est pas une lettre", [$contact->getId(), $idh]);
            return null;
        }
        return $bean;
    }
}
