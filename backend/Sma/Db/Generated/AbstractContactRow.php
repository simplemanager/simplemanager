<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for contact
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use ContactRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractContactRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'contact';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getCivility()
    {
        return $this->get('civility');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setCivility($value)
    {
        return $this->set('civility', $value);
    }

    final public function getFirstname()
    {
        return $this->get('firstname');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setFirstname($value)
    {
        return $this->set('firstname', $value);
    }

    final public function getLastname()
    {
        return $this->get('lastname');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setLastname($value)
    {
        return $this->set('lastname', $value);
    }

    final public function getFunction()
    {
        return $this->get('function');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setFunction($value)
    {
        return $this->set('function', $value);
    }

    final public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setEmail($value)
    {
        return $this->set('email', $value);
    }

    final public function getTel()
    {
        return $this->get('tel');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setTel($value)
    {
        return $this->set('tel', $value);
    }

    final public function getFax()
    {
        return $this->get('fax');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setFax($value)
    {
        return $this->set('fax', $value);
    }

    final public function getGsm()
    {
        return $this->get('gsm');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setGsm($value)
    {
        return $this->set('gsm', $value);
    }

    final public function getIdAddress()
    {
        return $this->get('id_address');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setIdAddress($value)
    {
        return $this->set('id_address', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\ContactRow
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
     * @return \Sma\Db\ContactRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getComment()
    {
        return $this->get('comment');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setComment($value)
    {
        return $this->set('comment', $value);
    }

    final public function getIdCompany()
    {
        return $this->get('id_company');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setIdCompany($value)
    {
        return $this->set('id_company', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getIsAccount()
    {
        return $this->get('is_account');
    }

    /**
     * @return \Sma\Db\ContactRow
     */
    final public function setIsAccount($value)
    {
        return $this->set('is_account', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\ContactRow
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
     * @return \Sma\Db\AddressRow
     */
    public function getRelatedAddressRowFromIdAddressFk()
    {
        return $this->getInternalFkRow($this->getIdAddress(), \Sma\Db\DbContainer::getAddressTable(), 'id');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIsAccountFk()
    {
        return $this->getInternalFkRow($this->getIsAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}