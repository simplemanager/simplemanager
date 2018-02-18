<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for log
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use LogRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractLogRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'log';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getLevel()
    {
        return $this->get('level');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setLevel($value)
    {
        return $this->set('level', $value);
    }

    final public function getMessage()
    {
        return $this->get('message');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setMessage($value)
    {
        return $this->set('message', $value);
    }

    final public function getPage()
    {
        return $this->get('page');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setPage($value)
    {
        return $this->set('page', $value);
    }

    final public function getIp()
    {
        return $this->get('ip');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setIp($value)
    {
        return $this->set('ip', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\LogRow
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
     * @return \Sma\Db\LogRow
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
     * @return \Sma\Db\LogRow
     */
    final public function setDateUpdate($value)
    {
        return $this->set('date_update', $value);
    }

    final public function getCategory()
    {
        return $this->get('category');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setCategory($value)
    {
        return $this->set('category', $value);
    }

    final public function getPageInfo()
    {
        return $this->get('page_info');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setPageInfo($value)
    {
        return $this->set('page_info', $value);
    }

    final public function getDump()
    {
        return $this->get('dump');
    }

    /**
     * @return \Sma\Db\LogRow
     */
    final public function setDump($value)
    {
        return $this->set('dump', $value);
    }
}