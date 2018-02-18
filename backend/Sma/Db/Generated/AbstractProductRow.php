<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for product
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use ProductRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractProductRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'product';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\ProductRow
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
     * @return \Sma\Db\ProductRow
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
     * @return \Sma\Db\ProductRow
     */
    final public function setUid($value)
    {
        return $this->set('uid', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setTitle($value)
    {
        return $this->set('title', $value);
    }

    final public function getPrice()
    {
        return $this->get('price');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setPrice($value)
    {
        return $this->set('price', $value);
    }

    final public function getCode()
    {
        return $this->get('code');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setCode($value)
    {
        return $this->set('code', $value);
    }

    final public function getPriceType()
    {
        return $this->get('price_type');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setPriceType($value)
    {
        return $this->set('price_type', $value);
    }

    final public function getTax()
    {
        return $this->get('tax');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setTax($value)
    {
        return $this->set('tax', $value);
    }

    final public function getUnit()
    {
        return $this->get('unit');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setUnit($value)
    {
        return $this->set('unit', $value);
    }

    final public function getDiscount()
    {
        return $this->get('discount');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setDiscount($value)
    {
        return $this->set('discount', $value);
    }

    final public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getBeanType()
    {
        return $this->get('bean_type');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setBeanType($value)
    {
        return $this->set('bean_type', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\ProductRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\ProductRow
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
     * @return \Sma\Db\ProductRow
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
}