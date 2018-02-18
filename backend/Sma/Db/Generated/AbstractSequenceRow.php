<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for sequence
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use SequenceRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractSequenceRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'sequence';

    protected $primaryKeyColumn = [
        'name',
        'id_account',
    ];

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\SequenceRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getName()
    {
        return $this->get('name');
    }

    /**
     * @return \Sma\Db\SequenceRow
     */
    final public function setName($value)
    {
        return $this->set('name', $value);
    }

    final public function getValue()
    {
        return $this->get('value');
    }

    /**
     * @return \Sma\Db\SequenceRow
     */
    final public function setValue($value)
    {
        return $this->set('value', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}