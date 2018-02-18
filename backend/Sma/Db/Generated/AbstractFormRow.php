<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for form
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use FormRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractFormRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'form';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\FormRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getClass()
    {
        return $this->get('class');
    }

    /**
     * @return \Sma\Db\FormRow
     */
    final public function setClass($value)
    {
        return $this->set('class', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\FormRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getFormValues()
    {
        return $this->get('form_values');
    }

    /**
     * @return \Sma\Db\FormRow
     */
    final public function setFormValues($value)
    {
        return $this->set('form_values', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}