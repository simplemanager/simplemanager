<?php
namespace Sma\Db\Addon;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Osf\Exception\ArchException;
use Sma\Session\Identity;
use DB;

/**
 * Gestion des tables d'événements
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait EventActions
{
    /**
     * Enregistre un événement
     * @param int $docId
     * @param int $idDocumentHistory
     * @param string|null $event
     * @param string|null $comment
     * @return $this
     */
    protected function registerEvent(int $docId, int $idDocumentHistory, string $event, ?string $comment = null, ?int $idAccount = null)
    {
        if (!in_array($event, BDB::EVENTS)) {
            throw new ArchException('Event [' . $event . '] unknown');
        }
        DB::getDocumentEventTable()->insert([
            'id_document' => $docId,
            'id_document_history' => $idDocumentHistory,
            'id_account' => $idAccount ?? Identity::getIdAccount(),
            'event' => $event,
            'comment' => $comment
        ]);
        return $this;
    }
    
}