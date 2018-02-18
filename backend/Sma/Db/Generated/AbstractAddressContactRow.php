<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for address_contact
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use AddressContactRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractAddressContactRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'address_contact';

    protected $primaryKeyColumn = [
        
    ];

    final public function getCivility()
    {
        return $this->get('civility');
    }

    /**
     * @return \Sma\Db\AddressContactRow
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
     * @return \Sma\Db\AddressContactRow
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
     * @return \Sma\Db\AddressContactRow
     */
    final public function setLastname($value)
    {
        return $this->set('lastname', $value);
    }

    final public function getAddress()
    {
        return $this->get('address');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setAddress($value)
    {
        return $this->set('address', $value);
    }

    final public function getPostalCode()
    {
        return $this->get('postal_code');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setPostalCode($value)
    {
        return $this->set('postal_code', $value);
    }

    final public function getCity()
    {
        return $this->get('city');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setCity($value)
    {
        return $this->set('city', $value);
    }

    final public function getCountry()
    {
        return $this->get('country');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setCountry($value)
    {
        return $this->set('country', $value);
    }

    final public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @return \Sma\Db\AddressContactRow
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
     * @return \Sma\Db\AddressContactRow
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
     * @return \Sma\Db\AddressContactRow
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
     * @return \Sma\Db\AddressContactRow
     */
    final public function setGsm($value)
    {
        return $this->set('gsm', $value);
    }

    final public function getFunction()
    {
        return $this->get('function');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setFunction($value)
    {
        return $this->set('function', $value);
    }

    final public function getIsAccount()
    {
        return $this->get('is_account');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setIsAccount($value)
    {
        return $this->set('is_account', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getIdContact()
    {
        return $this->get('id_contact');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setIdContact($value)
    {
        return $this->set('id_contact', $value);
    }

    final public function getIdCompany()
    {
        return $this->get('id_company');
    }

    /**
     * @return \Sma\Db\AddressContactRow
     */
    final public function setIdCompany($value)
    {
        return $this->set('id_company', $value);
    }
}