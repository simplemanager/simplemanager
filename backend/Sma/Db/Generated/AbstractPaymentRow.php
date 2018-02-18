<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for payment
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use PaymentRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractPaymentRow extends AbstractRowGateway
{

    protected $schemaKey = 'admin';

    protected $table = 'payment';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\PaymentRow
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
     * @return \Sma\Db\PaymentRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getAmount()
    {
        return $this->get('amount');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    final public function setAmount($value)
    {
        return $this->set('amount', $value);
    }

    final public function getDateBegin()
    {
        return $this->get('date_begin');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    final public function setDateBegin($value)
    {
        return $this->set('date_begin', $value);
    }

    final public function getDateEnd()
    {
        return $this->get('date_end');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    final public function setDateEnd($value)
    {
        return $this->set('date_end', $value);
    }

    final public function getIdProduct()
    {
        return $this->get('id_product');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    final public function setIdProduct($value)
    {
        return $this->set('id_product', $value);
    }

    final public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getComment()
    {
        return $this->get('comment');
    }

    /**
     * @return \Sma\Db\PaymentRow
     */
    final public function setComment($value)
    {
        return $this->set('comment', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\PaymentRow
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
     * @return \Sma\Db\PaymentRow
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
     * @return \Sma\Db\FormulaRow
     */
    public function getRelatedFormulaRowFromIdProductFk()
    {
        return $this->getInternalFkRow($this->getIdProduct(), \Sma\Db\DbContainer::getFormulaTable(), 'id');
    }
}