<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for image
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use ImageRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractImageRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'image';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getType()
    {
        return $this->get('type');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    final public function setType($value)
    {
        return $this->set('type', $value);
    }

    final public function getContent()
    {
        return $this->get('content');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    final public function setContent($value)
    {
        return $this->set('content', $value);
    }

    final public function getColor()
    {
        return $this->get('color');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    final public function setColor($value)
    {
        return $this->set('color', $value);
    }

    final public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @return \Sma\Db\ImageRow
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
     * @return \Sma\Db\ImageRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getBean()
    {
        return $this->get('bean');
    }

    /**
     * @return \Sma\Db\ImageRow
     */
    final public function setBean($value)
    {
        return $this->set('bean', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}