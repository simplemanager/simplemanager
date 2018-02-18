<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for form_stats
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use FormStatsRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractFormStatsRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'form_stats';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\FormStatsRow
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
     * @return \Sma\Db\FormStatsRow
     */
    final public function setClass($value)
    {
        return $this->set('class', $value);
    }

    final public function getFormValues()
    {
        return $this->get('form_values');
    }

    /**
     * @return \Sma\Db\FormStatsRow
     */
    final public function setFormValues($value)
    {
        return $this->set('form_values', $value);
    }
}