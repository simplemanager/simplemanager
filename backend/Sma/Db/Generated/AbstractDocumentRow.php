<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for document
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'document';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getIdRecipient()
    {
        return $this->get('id_recipient');
    }

    /**
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getTemplate()
    {
        return $this->get('template');
    }

    /**
     * @return \Sma\Db\DocumentRow
     */
    final public function setTemplate($value)
    {
        return $this->set('template', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\DocumentRow
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
     * @return \Sma\Db\DocumentRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    public function getRelatedContactRowFromIdRecipientFk()
    {
        return $this->getInternalFkRow($this->getIdRecipient(), \Sma\Db\DbContainer::getContactTable(), 'id');
    }
}