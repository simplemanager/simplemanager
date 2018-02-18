<?php
namespace Sma\Db;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Sma\Db\Generated\AbstractDocumentEventTable;
use Sma\Bean\InvoiceBean as IB;

/**
 * Table model for table document_event
 *
 * Use this class to complete AbstractDocumentEventTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class DocumentEventTable extends AbstractDocumentEventTable
{
    /**
     * Message lisible correspondant à l'événement $event
     * @param string $event
     * @return array|string|null
     */
    public static function getEventMessage(string $event = null, string $docType = null)
    {
        $events = [
            BDB::EVENT_CREATION         => __("Création"),
            BDB::EVENT_UPDATE           => __("Mise à jour"),
            BDB::EVENT_SENDING          => __("Envoi au destinataire"),
            BDB::EVENT_PROCESS          => $docType === IB::TYPE_INVOICE 
                                         ? __("Paiement") 
                                         : ($docType === IB::TYPE_ORDER 
                                             ? __("Signature") 
                                             : __("Validation")),
            BDB::EVENT_READ             => __("Lecture par le destinataire"),
            BDB::EVENT_DELETE           => __("Suppression"),
            BDB::EVENT_STATUS_CREATED   => __("État « brouillon »"),
            BDB::EVENT_STATUS_SENT      => __("État « envoyé »"),
            BDB::EVENT_STATUS_PROCESSED => $docType === IB::TYPE_INVOICE 
                                         ? __("État « payé »") 
                                         : ($docType === IB::TYPE_ORDER 
                                             ? __("État « signé »") 
                                             : __("État « traité »")),
            BDB::EVENT_STATUS_CANCELED  => __("État « annulé »")
        ];
        if ($event === null) {
            return $events;
        }
        if (isset($events[$event])) {
            return $events[$event];
        }
        return null;
    }
}