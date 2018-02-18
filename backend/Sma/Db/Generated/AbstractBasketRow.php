<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for basket
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use BasketRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractBasketRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'basket';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdInvoice()
    {
        return $this->get('id_invoice');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    final public function setIdInvoice($value)
    {
        return $this->set('id_invoice', $value);
    }

    final public function getIdProduct()
    {
        return $this->get('id_product');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    final public function setIdProduct($value)
    {
        return $this->set('id_product', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getQuantity()
    {
        return $this->get('quantity');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    final public function setQuantity($value)
    {
        return $this->set('quantity', $value);
    }

    final public function getDiscount()
    {
        return $this->get('discount');
    }

    /**
     * @return \Sma\Db\BasketRow
     */
    final public function setDiscount($value)
    {
        return $this->set('discount', $value);
    }

    /**
     * @return \Sma\Db\InvoiceRow
     */
    public function getRelatedInvoiceRowFromIdInvoiceFk()
    {
        return $this->getInternalFkRow($this->getIdInvoice(), \Sma\Db\DbContainer::getInvoiceTable(), 'id');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    public function getRelatedProductRowFromIdProductFk()
    {
        return $this->getInternalFkRow($this->getIdProduct(), \Sma\Db\DbContainer::getProductTable(), 'id');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}