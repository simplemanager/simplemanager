<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for campaign
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use CampaignRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractCampaignRow extends AbstractRowGateway
{

    protected $schemaKey = 'admin';

    protected $table = 'campaign';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getCode()
    {
        return $this->get('code');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setCode($value)
    {
        return $this->set('code', $value);
    }

    final public function getProviderName()
    {
        return $this->get('provider_name');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setProviderName($value)
    {
        return $this->set('provider_name', $value);
    }

    final public function getProviderUrl()
    {
        return $this->get('provider_url');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setProviderUrl($value)
    {
        return $this->set('provider_url', $value);
    }

    final public function getProviderIdCompany()
    {
        return $this->get('provider_id_company');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setProviderIdCompany($value)
    {
        return $this->set('provider_id_company', $value);
    }

    final public function getAccessCount()
    {
        return $this->get('access_count');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setAccessCount($value)
    {
        return $this->set('access_count', $value);
    }

    final public function getCost()
    {
        return $this->get('cost');
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    final public function setCost($value)
    {
        return $this->set('cost', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\CampaignRow
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
     * @return \Sma\Db\CampaignRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    /**
     * @return \Sma\Db\CompanyRow
     */
    public function getRelatedCompanyRowFromProviderIdCompanyFk()
    {
        return $this->getInternalFkRow($this->getProviderIdCompany(), \Sma\Db\DbContainer::getCompanyTable(), 'id');
    }
}