<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for ticket_log
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use TicketLogRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractTicketLogRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'ticket_log';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdTicket()
    {
        return $this->get('id_ticket');
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    final public function setIdTicket($value)
    {
        return $this->set('id_ticket', $value);
    }

    final public function getComment()
    {
        return $this->get('comment');
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    final public function setComment($value)
    {
        return $this->set('comment', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    final public function setDateInsert($value)
    {
        return $this->set('date_insert', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\TicketLogRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }

    /**
     * @return \Sma\Db\TicketRow
     */
    public function getRelatedTicketRowFromIdTicketFk()
    {
        return $this->getInternalFkRow($this->getIdTicket(), \Sma\Db\DbContainer::getTicketTable(), 'id');
    }
}