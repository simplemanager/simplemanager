<?php
namespace Sma\Db;

use Osf\Pdf\Document\Bean\BaseDocumentBean as BDB;
use Osf\Exception\ArchException;
use Osf\Helper\Mysql;
use Osf\Stream\Debug;
use Sma\Db\Generated\AbstractDocumentTable;
use Sma\Pdf\DocumentInterface;
use Sma\Session\Identity;
use Sma\Bean\DocumentBeanInterface;
use Sma\Bean\InvoiceBean as IB;
use Sma\Bean\LetterBean;
use Sma\Container as C;
use Sma\Cache as SC;
use Sma\Log;
use DB;

/**
 * Table model for table document
 *
 * Use this class to complete AbstractDocumentTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class DocumentTable extends AbstractDocumentTable
{
    use Addon\SafeActions;
    use Addon\EventActions;
    
    const SEARCH_CATEGORY = 'document';
    
    /**
     * @param DocumentInterface $doc
     * @param string|null $eventComment
     * @return int|null doc id
     * @throws ArchException
     */
    public function saveDocument(DocumentInterface $doc, ?string $eventComment = null): ?int
    {
        // Construction du row pour l'insertion en base
        $docMain = $this->getDocumentRow($doc);
        
        // Update si l'id existe
        /* @var $bean Osf\Pdf\Document\Bean\BaseDocumentBean */
        $bean = $doc->getBean();
        $bean->setDate();
        $update = false;
        if ($id = $bean->getId()) {
            $where = ['id' => $id, 'id_account' => Identity::getIdAccount()];
            $affectedRows = $this->update($docMain, $where);
            if ($affectedRows !== 1) {
                throw new ArchException('Affected rows incorrect, attendu [1], retourné [' . $affectedRows . ']');
            }
            $update = true;
        }
        
        // Ajout sinon
        else {
            $docMain['uid'] = DB::getSequenceTable()->nextValue('document');
            $this->insert($docMain);
            $id = $this->lastInsertValue;
        }
        
        // Ajout du premier élément d'historique
        $idHistory = $this->addDocHistory($doc, $id);
        
        // Si le document est une facture ou dérivé, on met à jour dans la table
        // des factures. S'il s'agit d'une création de document, on crée une 
        // nouvelle facture/devis/commande
        if ($bean instanceof IB) {
            $bean->setIdDocument($id);
            $bean->setIdDocumentHistory($idHistory);
            $update || $bean->setIdInvoice(null);
            DB::getInvoiceTable()->saveInvoice($bean, $eventComment);
        }
        
        // Enregistrement de l'événement si ce n'est pas un invoice (car saveInvoice le fait déjà)
        else {
            $this->registerEvent($id, $idHistory, $update ? BDB::EVENT_UPDATE : BDB::EVENT_CREATION, $eventComment);
        }
        
        // Mise à jour des données de recherche
        self::updateSearchIndex($bean);
        
        // id du document
        return $id;
    }
    
    /**
     * Mise à jour d'un document
     * @param DocumentInterface $doc
     * @param int $id
     * @deprecated
     */
//    public function updateDocument(DocumentInterface $doc, int $id)
//    {
//        // Mise à jour du document
//        $docMain = $this->getDocumentRow($doc);
//        $doc->getBean()->setId($id);
//        $this->update($docMain, ['id' => $id, 'id_account' => Identity::getIdAccount()]);
//        
//        // Enregistrement de l'événement
//        
//        // Ajout dans l'historique
//        $this->addDocHistory($doc, $id);
//        
//        // Mise à jour des données de recherche
//        self::updateSearchIndex($doc->getBean(), $doc->getType());
//    }
    
    /**
     * Récupère le document et son historique 
     * @param int $id
     * @param int $history
     * @return array ['document' => ..., 'history' => ...]
     */
    public function getDocument(int $id, $history = null, bool $includeDump = false, bool $allHistory = false)
    {
        $docRow = $this->findSafe($id);
        $hstRow = $this->findDocumentHistory($id, $history, $includeDump, $allHistory);
        return ['document' => $docRow, 'history' => $hstRow];
    }
    
    /**
     * @param int $id
     * @param type $history
     * @return \Sma\Bean\DocumentBeanInterface
     */
    public function getBean(int $id, $history = null): DocumentBeanInterface
    {
        $hstRow = $this->findDocumentHistory($id, $history);
        $bean = $hstRow['source'];
        
        // Vérifications sur le type de bean récupéré
        if (!($bean instanceof DocumentBeanInterface)) {
            Log::error('Not a document bean interface ?', 'DB', Debug::getType($bean));
            throw new ArchException('Not a document bean interface');
        }
        if (!($bean instanceof BDB)) {
            Log::error('Not a base document bean ?', 'DB', Debug::getType($bean));
            throw new ArchException('Not a base document bean');
        }
        
        // Mise à jour du statut s'il s'agit du dernier historique en date
        if (!$history) {
            $status = $this->findSafe($id)->getStatus();
            $status = $status ?: BDB::STATUS_CREATED;
            $bean->setStatus($status);
        }
        
        return $bean;
    }
    
    /**
     * Récupère l'historique d'un document (par défaut la dernière version)
     * @param int $id
     * @param int|null $idHistory
     * @param bool $includeDump
     * @param bool $returnAllRows
     * @param bool $buildBean
     * @return array
     * @throws ArchException
     */
    protected function findDocumentHistory(
            int  $id, 
            ?int $idHistory = null, 
            bool $includeDump = false, 
            bool $returnAllRows = false, 
            bool $buildBean = true)
    {
        $rows = 'id, source, date_insert' . ($includeDump ? ', dump' : '');
        $sql = 'SELECT ' . $rows . ' FROM `document_history` WHERE id_document=? ';
        $values = [$id];
        if ($idHistory !== null) {
            $sql .= 'AND id=?';
            $values[] = $idHistory;
        } else {
            $sql .= 'ORDER BY id DESC ' . ($returnAllRows ? '' : 'LIMIT 0, 1');
        }
        $result = $this->prepare($sql)->execute($values);
        if (!$returnAllRows && $result->count() !== 1) {
            throw new ArchException('Unable to find document history [' . $id . '], [' . $idHistory . ']');
        }
        if ($returnAllRows) {
            return $result;
        }
        $row = $result->current();
        if ($buildBean) {
            $row['source'] = unserialize($row['source']);
            $row['source']->setId($id);
        }
        return $row;
    }
    
    /**
     * Construit l'enregistrement à partir du document
     * @param DocumentInterface $doc
     * @return array
     */
    protected function getDocumentRow(DocumentInterface $doc)
    {
        // Va chercher le sujet dans le bean
        $bean = $doc->getBean();
        if ($bean instanceof LetterBean) {
            $libs = $bean->getLibs();
            if (isset($libs['Objet :'])) {
                $subject = $libs['Objet :'];
            }
        }
        $subject = isset($subject) ? $subject : $bean->getTitle();
        
        // Construit le row à partir des informations du bean
        return [
            'id_account'   => Identity::getIdAccount(),
            'id_recipient' => $bean->getRecipient()->getId(),
            'type'         => $bean->getType(),
            'status'       => $bean->getStatus(),
            'template'     => $bean->getTemplate(),
            'title'        => $bean->getTitle(),
            'subject'      => $subject,
            'description'  => $bean->getDescription()
        ];
    }
    
    /**
     * Ajout d'une entrée d'historique
     * @param DocumentInterface $doc
     * @param int $id
     * @return type
     */
    protected function addDocHistory(DocumentInterface $doc, int $id)
    {
        $doc->getBean()->setId($id);
        $docHistory = [
            'id_document' => $id,
            'id_account' => Identity::getIdAccount(),
            'dump' => $doc->getDump(),
            'source' => serialize($doc->getBean()),
            'hash' => $doc->getBean()->getHashLastBuilded(true)
        ];
        $docHistoryTable = DB::getDocumentHistoryTable();
        $docHistoryTable->insert($docHistory);
        return $docHistoryTable->lastInsertValue;
    }
    
    /**
     * Récupération des documents de l'utilisateur courant
     * @param string $type
     * @return \Iterator
     */
    public function getDocuments($type = null, array $settings = [])
    {
        $sorts = [
            'oa'  => 'document.title ASC',
            'od'  => 'document.title DESC',
            'da'  => 'document.description ASC',
            'dd'  => 'document.description DESC',
            'dca' => 'document.date_insert ASC',
            'dcd' => 'document.date_insert DESC',
            'dua' => 'document.date_update ASC',
            'dud' => 'document.date_update DESC',
        ];
        
        $sort = isset($settings['s']) && isset($sorts[$settings['s']]) ? $sorts[$settings['s']] : $sorts['dcd'];
        
        $params = [Identity::getIdAccount()];
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE id_account=? ';
        if ($type !== null) {
            $sql .= 'AND type=? ';
            $params[] = $type;
        }
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND document.date_update >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND document.date_update <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND (document.title LIKE ? OR document.description LIKE ?) ';
            $like = Mysql::like($settings['q']);
            $params[] = $like;
            $params[] = $like;
        }
        if (isset($settings['st']) && $settings['st']) {
            $sql .= 'AND document.status = ? ';
            $params[] = $settings['st'];
        }
        $sql .= ' ORDER BY ' . $sort;
        return $this->prepare($sql)->execute($params);
    }
    
    /**
     * Mise à jour d'un document dans les données de recherche
     * @param int $id
     * @param DocumentBeanInterface $bean
     * @param string $type
     * @param bool $cleanItem
     */
    protected static function updateSearchIndex(DocumentBeanInterface $bean, bool $cleanItem = true, $idAccount = null)
    {
        // Nettoyage
        if ($cleanItem) {
            C::getSearch()->cleanAutocomplete(self::SEARCH_CATEGORY, $bean->getId());
        }
        
        // Valeurs utiles pour la liste d'autocomplétion
        $values = [
            'id'    => $bean->getId(),
            'title' => $bean->getTitle(),
            'desc'  => $bean->getDescription()
        ];
        
        // Indexation
        $subCategories = [$bean->getType()];
        if ($bean instanceof IB) {
            $subCategories[] = IB::TYPE_ALL;
        }
        C::getSearch()->indexAutocompleteItem(
                $bean->getSearchData(), (string) $bean->getTitle(), $values, 
                self::SEARCH_CATEGORY, $bean->getId(), $bean->buildUrl(), 
                8, $idAccount, serialize($bean), $subCategories);
    }

    /**
     * Indexation des lettres et factures
     * @param int $idAccount
     * @return $this
     */
    public function indexAllDocumentsForSearchEngine($idAccount = null)
    {
        // Récupération
        $idAccount = (int) ($idAccount ?? Identity::getIdAccount());
        $sql = 'SELECT document.id, document.type, document_history.source '
                . 'FROM document_history, document '
                . 'WHERE document.id=document_history.id_document '
                . 'AND document_history.id IN ('
                . 'SELECT max(document_history.id) as dh_id FROM document_history, document '
                . 'WHERE document.id=document_history.id_document '
                . 'AND document.id_account=' . $idAccount . ' GROUP BY document.id)';
        $rows = $this->execute($sql);
        
        // Suppression de l'ensemble des données d'indexation
        C::getSearch()->cleanAutocomplete(self::SEARCH_CATEGORY, null, $idAccount);
        
        // Indexation de l'ensemble des documents
        foreach ($rows as $doc) {
            $bean = unserialize($doc['source']);
            $bean->setId($doc['id']);
            self::updateSearchIndex($bean, false, $idAccount);
        }
        
        return $this;
    }
    
    /**
     * Mise à jour du statut d'un document
     * @param int $id
     * @param string $status
     * @param string|null $event
     * @param bool $safe
     * @param string|null $comment
     * @param bool $checkIfAllowed
     * @return bool
     */
    public function updateStatus(int $id, string $status, string $event = null, bool $safe = true, ?string $comment = null, bool $checkIfAllowed = false): bool
    {
        $retVal = false;
        if (!$id || $id < 0) {
            Log::hack("CHST : Mauvais id [" . $id . ']');
        } else if (!in_array($status, BDB::STATUSES)) {
            Log::hack("CHST : Changement pour un statut inconnu [" . $status . ']');
        } else if (!is_null($event) && !in_array($event, BDB::EVENTS)) {
            Log::hack("CHST : Action inconnue [" . $event . ']');
        } else {
            try {
                $doc = $safe ? $this->findSafe($id) : $this->find($id);
                $history = $this->findDocumentHistory($doc->getId(), null, false, false, false);
                $idAccount = Identity::getIdAccount() ?? ($safe ? null : $doc->getIdAccount());
                if (!$doc) {
                    Log::hack("Document pas trouvé ou n'appartenant pas à l'utilisateur courant [" . $id . ']');
                } else if (!$history) {
                    Log::hack("Document pas trouvé ou n'appartenant pas à l'utilisateur courant [" . $doc->getId() . ']');
                } else if (!$idAccount) {
                    Log::hack("Id du compte non trouvé (utilisateur non logué et accès non safe)", [Debug::getType($doc), Debug::getType($history), $safe, $idAccount]);
                } else if (!($doc instanceof DocumentRow) || !is_array($history)) {
                    Log::hack("Mauvais type de document", [Debug::getType($doc), Debug::getType($history)]);
                } else if ($checkIfAllowed && !$this->checkAllowed($doc, $status)) {
                    Log::hack(__("Action interdite : modification manuelle de [" . $doc->getType() . ':' . $doc->getStatus() . '] vers [' . $status . ']'));
                } else {
                    C::getCacheSma()->cleanItem(SC::C_DOCUMENT, $doc->getId());
                    $doc->setStatus($status)->save();
                    $this->registerEvent($doc->getId(), $history['id'], $event, $comment, $idAccount);
                    $retVal = true;
                }
            } catch (Exception $e) {
                Log::error($e->getMessage(), 'CHST', $e);
            }
        }
        return $retVal;
    }
    
    /**
     * Contrôle ce que le client peut faire manuellement comme changement d'état
     * @param \Sma\Db\DocumentRow $doc
     * @param string $status
     * @return bool
     */
    protected function checkAllowed(DocumentRow $doc, string $status): bool
    {
        return isset(IB::ALLOWED_OWNER_ACTIONS[$doc->getType()][$doc->getStatus()][$status]);
    }
    
    /**
     * Remplis les id_recipient avec les id_contact des récipients trouvés dans document_history
     * @throws ArchException
     */
    public function fixDocumentRecipients()
    {
        $sql = 'SELECT id_document, source '
                . 'FROM document_history, document '
                . 'WHERE document.id = document_history.id_document '
                . 'AND document.id_recipient IS NULL '
                . 'AND document_history.id IN (SELECT MAX(id) FROM document_history GROUP BY id_document)';
        $rows = DB::getDocumentHistoryTable()->execute($sql);
        foreach ($rows as $row) {
            if (!$row['source']) {
                throw new ArchException('No source for document id [' . $row['id_document'] . ']');
            }
            $bean = unserialize($row['source']);
            if (!($bean instanceof DocumentBeanInterface)) {
                throw new ArchException('Not a document bean interface [' . $row['id_document'] . ']');
            }
            $idRecipient = $bean->getRecipient()->getId();
            if ($idRecipient) {
                if (DB::getContactTable()->find($idRecipient)) {
                    DB::getDocumentTable()->find($row['id_document'])->setIdRecipient($idRecipient)->save();
                }
            }
        }
    }
    
    /**
     * Remplis les sujets des documents depuis leurs beans
     * @throws ArchException
     */
    public function fixDocumentSubjects()
    {
        $sql = 'SELECT id_document, source '
                . 'FROM document_history, document '
                . 'WHERE document.id = document_history.id_document '
                . 'AND document.subject IS NULL '
                . 'AND document_history.id IN (SELECT MAX(id) FROM document_history GROUP BY id_document)';
        $rows = DB::getDocumentHistoryTable()->execute($sql);
        foreach ($rows as $row) {
            if (!$row['source']) {
                throw new ArchException('No source for document id [' . $row['id_document'] . ']');
            }
            $bean = unserialize($row['source']);
            if (!($bean instanceof DocumentBeanInterface)) {
                throw new ArchException('Not a document bean interface [' . $row['id_document'] . ']');
            }
            $subject = null;
            if ($bean instanceof LetterBean) {
                $libs = $bean->getLibs();
                if (isset($libs['Objet :'])) {
                    $subject = $libs['Objet :'];
                }
            }
            if ($subject === null) {
                $subject = $bean->getTitle();
            }
            if ($subject !== null) {
                DB::getDocumentTable()->find($row['id_document'])->setSubject($subject)->save();
            }
        }
    }
}
