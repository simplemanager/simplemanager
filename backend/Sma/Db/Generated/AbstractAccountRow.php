<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for account
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use AccountRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractAccountRow extends AbstractRowGateway
{

    protected $schemaKey = 'admin';

    protected $table = 'account';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getEmail()
    {
        return $this->get('email');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setEmail($value)
    {
        return $this->set('email', $value);
    }

    final public function getPassword()
    {
        return $this->get('password');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setPassword($value)
    {
        return $this->set('password', $value);
    }

    final public function getFirstname()
    {
        return $this->get('firstname');
    }

    /**
     * @return \Sma\Db\AccountRow
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
     * @return \Sma\Db\AccountRow
     */
    final public function setLastname($value)
    {
        return $this->set('lastname', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\AccountRow
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
     * @return \Sma\Db\AccountRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getIdCampaign()
    {
        return $this->get('id_campaign');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setIdCampaign($value)
    {
        return $this->set('id_campaign', $value);
    }

    final public function getComment()
    {
        return $this->get('comment');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setComment($value)
    {
        return $this->set('comment', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }

    /**
     * @return \Sma\Db\CampaignRow
     */
    public function getRelatedCampaignRowFromIdCampaignFk()
    {
        return $this->getInternalFkRow($this->getIdCampaign(), \Sma\Db\DbContainer::getCampaignTable(), 'id');
    }
}