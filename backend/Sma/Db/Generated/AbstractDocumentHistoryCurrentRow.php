<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for document_history_current
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentHistoryCurrentRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentHistoryCurrentRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'document_history_current';

    protected $primaryKeyColumn = [
        
    ];

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
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
     * @return \Sma\Db\DocumentHistoryCurrentRow
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
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setIdDocumentHistory($value)
    {
        return $this->set('id_document_history', $value);
    }

    final public function getIdRecipient()
    {
        return $this->get('id_recipient');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setIdRecipient($value)
    {
        return $this->set('id_recipient', $value);
    }

    final public function getUid()
    {
        return $this->get('uid');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setUid($value)
    {
        return $this->set('uid', $value);
    }

    final public function getType()
    {
        return $this->get('type');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setType($value)
    {
        return $this->set('type', $value);
    }

    final public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setTitle($value)
    {
        return $this->set('title', $value);
    }

    final public function getSubject()
    {
        return $this->get('subject');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setSubject($value)
    {
        return $this->set('subject', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setTemplate($value)
    {
        return $this->set('template', $value);
    }

    final public function getSource()
    {
        return $this->get('source');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setSource($value)
    {
        return $this->set('source', $value);
    }

    final public function getDump()
    {
        return $this->get('dump');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setDump($value)
    {
        return $this->set('dump', $value);
    }

    final public function getHash()
    {
        return $this->get('hash');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setHash($value)
    {
        return $this->set('hash', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setDateInsert($value)
    {
        return $this->set('date_insert', $value);
    }

    final public function getDateUpdate()
    {
        return $this->get('date_update');
    }

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }
}