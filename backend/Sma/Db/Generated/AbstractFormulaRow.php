<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for formula
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use FormulaRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractFormulaRow extends AbstractRowGateway
{

    protected $schemaKey = 'admin';

    protected $table = 'formula';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdApplication()
    {
        return $this->get('id_application');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    final public function setIdApplication($value)
    {
        return $this->set('id_application', $value);
    }

    final public function getName()
    {
        return $this->get('name');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    final public function setName($value)
    {
        return $this->set('name', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    final public function setDescription($value)
    {
        return $this->set('description', $value);
    }

    final public function getDuration()
    {
        return $this->get('duration');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    final public function setDuration($value)
    {
        return $this->set('duration', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\FormulaRow
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
     * @return \Sma\Db\FormulaRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\FormulaRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }
}