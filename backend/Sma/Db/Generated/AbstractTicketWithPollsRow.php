<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for ticket_with_polls
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use TicketWithPollsRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractTicketWithPollsRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'ticket_with_polls';

    protected $primaryKeyColumn = [
        
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setCategory($value)
    {
        return $this->set('category', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setTitle($value)
    {
        return $this->set('title', $value);
    }

    final public function getContent()
    {
        return $this->get('content');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setContent($value)
    {
        return $this->set('content', $value);
    }

    final public function getResponse()
    {
        return $this->get('response');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setResponse($value)
    {
        return $this->set('response', $value);
    }

    final public function getStatus()
    {
        return $this->get('status');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setStatus($value)
    {
        return $this->set('status', $value);
    }

    final public function getVisibility()
    {
        return $this->get('visibility');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setVisibility($value)
    {
        return $this->set('visibility', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
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
     * @return \Sma\Db\TicketWithPollsRow
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
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getPollCount()
    {
        return $this->get('poll_count');
    }

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    final public function setPollCount($value)
    {
        return $this->set('poll_count', $value);
    }
}