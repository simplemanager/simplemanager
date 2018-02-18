<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for company
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use CompanyRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractCompanyRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'company';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\CompanyRow
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
     * @return \Sma\Db\CompanyRow
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
     * @return \Sma\Db\CompanyRow
     */
    final public function setUid($value)
    {
        return $this->set('uid', $value);
    }

    final public function getHash()
    {
        return $this->get('hash');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setHash($value)
    {
        return $this->set('hash', $value);
    }

    final public function getType()
    {
        return $this->get('type');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setType($value)
    {
        return $this->set('type', $value);
    }

    final public function getLegalStatus()
    {
        return $this->get('legal_status');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setLegalStatus($value)
    {
        return $this->set('legal_status', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setTitle($value)
    {
        return $this->set('title', $value);
    }

    final public function getTel()
    {
        return $this->get('tel');
    }

    /**
     * @return \Sma\Db\CompanyRow
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
     * @return \Sma\Db\CompanyRow
     */
    final public function setFax($value)
    {
        return $this->set('fax', $value);
    }

    final public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setEmail($value)
    {
        return $this->set('email', $value);
    }

    final public function getTvaIntra()
    {
        return $this->get('tva_intra');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setTvaIntra($value)
    {
        return $this->set('tva_intra', $value);
    }

    final public function getChargeWithTax()
    {
        return $this->get('charge_with_tax');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setChargeWithTax($value)
    {
        return $this->set('charge_with_tax', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getIdAddress()
    {
        return $this->get('id_address');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setIdAddress($value)
    {
        return $this->set('id_address', $value);
    }

    final public function getIdAddressDelivery()
    {
        return $this->get('id_address_delivery');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setIdAddressDelivery($value)
    {
        return $this->set('id_address_delivery', $value);
    }

    final public function getIdContact()
    {
        return $this->get('id_contact');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setIdContact($value)
    {
        return $this->set('id_contact', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\CompanyRow
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
     * @return \Sma\Db\CompanyRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getUrl()
    {
        return $this->get('url');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setUrl($value)
    {
        return $this->set('url', $value);
    }

    final public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getIdLogo()
    {
        return $this->get('id_logo');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setIdLogo($value)
    {
        return $this->set('id_logo', $value);
    }

    final public function getIdCompany()
    {
        return $this->get('id_company');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setIdCompany($value)
    {
        return $this->set('id_company', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }

    /**
     * @return \Sma\Db\AddressRow
     */
    public function getRelatedAddressRowFromIdAddressFk()
    {
        return $this->getInternalFkRow($this->getIdAddress(), \Sma\Db\DbContainer::getAddressTable(), 'id');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    public function getRelatedImageRowFromIdLogoFk()
    {
        return $this->getInternalFkRow($this->getIdLogo(), \Sma\Db\DbContainer::getImageTable(), 'id');
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
    public function getRelatedContactRowFromIdContactFk()
    {
        return $this->getInternalFkRow($this->getIdContact(), \Sma\Db\DbContainer::getContactTable(), 'id');
    }

    /**
     * @return \Sma\Db\AddressRow
     */
    public function getRelatedAddressRowFromIdAddressDeliveryFk()
    {
        return $this->getInternalFkRow($this->getIdAddressDelivery(), \Sma\Db\DbContainer::getAddressTable(), 'id');
    }
}