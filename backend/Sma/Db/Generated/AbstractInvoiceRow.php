<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for invoice
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use InvoiceRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractInvoiceRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'invoice';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\InvoiceRow
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
     * @return \Sma\Db\InvoiceRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getUid()
    {
        return $this->get('uid');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setUid($value)
    {
        return $this->set('uid', $value);
    }

    final public function getIdProvider()
    {
        return $this->get('id_provider');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setIdProvider($value)
    {
        return $this->set('id_provider', $value);
    }

    final public function getIdRecipient()
    {
        return $this->get('id_recipient');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setIdRecipient($value)
    {
        return $this->set('id_recipient', $value);
    }

    final public function getIdDocument()
    {
        return $this->get('id_document');
    }

    /**
     * @return \Sma\Db\InvoiceRow
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
     * @return \Sma\Db\InvoiceRow
     */
    final public function setIdDocumentHistory($value)
    {
        return $this->set('id_document_history', $value);
    }

    final public function getCode()
    {
        return $this->get('code');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setCode($value)
    {
        return $this->set('code', $value);
    }

    final public function getProductCount()
    {
        return $this->get('product_count');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setProductCount($value)
    {
        return $this->set('product_count', $value);
    }

    final public function getTotalHt()
    {
        return $this->get('total_ht');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setTotalHt($value)
    {
        return $this->set('total_ht', $value);
    }

    final public function getTotalTtc()
    {
        return $this->get('total_ttc');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setTotalTtc($value)
    {
        return $this->set('total_ttc', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\InvoiceRow
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
     * @return \Sma\Db\InvoiceRow
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
     * @return \Sma\Db\InvoiceRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    public function getRelatedCompanyRowFromIdProviderFk()
    {
        return $this->getInternalFkRow($this->getIdProvider(), \Sma\Db\DbContainer::getCompanyTable(), 'id');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    public function getRelatedContactRowFromIdRecipientFk()
    {
        return $this->getInternalFkRow($this->getIdRecipient(), \Sma\Db\DbContainer::getContactTable(), 'id');
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