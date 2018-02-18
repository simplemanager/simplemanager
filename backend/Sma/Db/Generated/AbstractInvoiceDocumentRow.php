<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for invoice_document
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use InvoiceDocumentRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractInvoiceDocumentRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'invoice_document';

    protected $primaryKeyColumn = [
        
    ];

    final public function getIdInvoice()
    {
        return $this->get('id_invoice');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setIdInvoice($value)
    {
        return $this->set('id_invoice', $value);
    }

    final public function getIdDocument()
    {
        return $this->get('id_document');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setIdDocument($value)
    {
        return $this->set('id_document', $value);
    }

    final public function getUidInvoice()
    {
        return $this->get('uid_invoice');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setUidInvoice($value)
    {
        return $this->set('uid_invoice', $value);
    }

    final public function getUidDocument()
    {
        return $this->get('uid_document');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setUidDocument($value)
    {
        return $this->set('uid_document', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getIdProvider()
    {
        return $this->get('id_provider');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setIdRecipient($value)
    {
        return $this->set('id_recipient', $value);
    }

    final public function getIdDocumentHistory()
    {
        return $this->get('id_document_history');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getType()
    {
        return $this->get('type');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setSubject($value)
    {
        return $this->set('subject', $value);
    }

    final public function getDocumentDescription()
    {
        return $this->get('document_description');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setDocumentDescription($value)
    {
        return $this->set('document_description', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
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
     * @return \Sma\Db\InvoiceDocumentRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }
}