<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for search
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use SearchRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractSearchRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'search';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getTitle()
    {
        return $this->get('title');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setTitle($value)
    {
        return $this->set('title', $value);
    }

    final public function getDoc()
    {
        return $this->get('doc');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setDoc($value)
    {
        return $this->set('doc', $value);
    }

    final public function getDocUid()
    {
        return $this->get('doc_uid');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setDocUid($value)
    {
        return $this->set('doc_uid', $value);
    }

    final public function getSearchContent()
    {
        return $this->get('search_content');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setSearchContent($value)
    {
        return $this->set('search_content', $value);
    }

    final public function getLevel()
    {
        return $this->get('level');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setLevel($value)
    {
        return $this->set('level', $value);
    }

    final public function getUrl()
    {
        return $this->get('url');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setUrl($value)
    {
        return $this->set('url', $value);
    }

    final public function getParams()
    {
        return $this->get('params');
    }

    /**
     * @return \Sma\Db\SearchRow
     */
    final public function setParams($value)
    {
        return $this->set('params', $value);
    }

    final public function getDateInsert()
    {
        return $this->get('date_insert');
    }

    /**
     * @return \Sma\Db\SearchRow
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
     * @return \Sma\Db\SearchRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }
}