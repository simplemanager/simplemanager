<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for ticket_poll
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use TicketPollRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractTicketPollRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'ticket_poll';

    protected $primaryKeyColumn = [
        'id_account',
        'id_ticket',
    ];

    final public function getIdTicket()
    {
        return $this->get('id_ticket');
    }

    /**
     * @return \Sma\Db\TicketPollRow
     */
    final public function setIdTicket($value)
    {
        return $this->set('id_ticket', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\TicketPollRow
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

    /**
     * @return \Sma\Db\AccountRow
     */
    public function getRelatedAccountRowFromIdAccountFk()
    {
        return $this->getInternalFkRow($this->getIdAccount(), \Sma\Db\DbContainer::getAccountTable(), 'id');
    }
}