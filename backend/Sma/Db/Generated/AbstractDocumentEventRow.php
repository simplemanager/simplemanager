<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for document_event
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentEventRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentEventRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'document_event';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getIdDocument()
    {
        return $this->get('id_document');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setIdDocument($value)
    {
        return $this->set('id_document', $value);
    }

    final public function getIdDocumentHistory()
    {
        return $this->get('id_document_history');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setIdDocumentHistory($value)
    {
        return $this->set('id_document_history', $value);
    }

    final public function getEvent()
    {
        return $this->get('event');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setEvent($value)
    {
        return $this->set('event', $value);
    }

    final public function getDate()
    {
        return $this->get('date');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setDate($value)
    {
        return $this->set('date', $value);
    }

    final public function getComment()
    {
        return $this->get('comment');
    }

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    final public function setComment($value)
    {
        return $this->set('comment', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }

    /**
     * @return \Sma\Db\DocumentRow
     */
    public function getRelatedDocumentRowFromIdDocumentFk()
    {
        return $this->getInternalFkRow($this->getIdDocument(), \Sma\Db\DbContainer::getDocumentTable(), 'id');
    }

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    public function getRelatedDocumentHistoryRowFromIdDocumentHistoryFk()
    {
        return $this->getInternalFkRow($this->getIdDocumentHistory(), \Sma\Db\DbContainer::getDocumentHistoryTable(), 'id');
    }
}