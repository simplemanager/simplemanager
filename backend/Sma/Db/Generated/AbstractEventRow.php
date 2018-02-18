<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for event
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use EventRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractEventRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'event';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\EventRow
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
     * @return \Sma\Db\EventRow
     */
    final public function setCode($value)
    {
        return $this->set('code', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\EventRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\EventRow
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