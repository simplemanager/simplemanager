<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for address
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use AddressRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractAddressRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'address';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\AddressRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getAddress()
    {
        return $this->get('address');
    }

    /**
     * @return \Sma\Db\AddressRow
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
     * @return \Sma\Db\AddressRow
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
     * @return \Sma\Db\AddressRow
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
     * @return \Sma\Db\AddressRow
     */
    final public function setCountry($value)
    {
        return $this->set('country', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\AddressRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}