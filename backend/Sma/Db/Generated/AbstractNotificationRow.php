<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for notification
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use NotificationRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractNotificationRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'notification';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\NotificationRow
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
     * @return \Sma\Db\NotificationRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    final public function setDateInsert($value)
    {
        return $this->set('date_insert', $value);
    }

    final public function getDateEnd()
    {
        return $this->get('date_end');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    final public function setDateEnd($value)
    {
        return $this->set('date_end', $value);
    }

    final public function getIcon()
    {
        return $this->get('icon');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    final public function setIcon($value)
    {
        return $this->set('icon', $value);
    }

    final public function getColor()
    {
        return $this->get('color');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    final public function setColor($value)
    {
        return $this->set('color', $value);
    }

    final public function getContent()
    {
        return $this->get('content');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    final public function setContent($value)
    {
        return $this->set('content', $value);
    }

    final public function getLink()
    {
        return $this->get('link');
    }

    /**
     * @return \Sma\Db\NotificationRow
     */
    final public function setLink($value)
    {
        return $this->set('link', $value);
    }

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}