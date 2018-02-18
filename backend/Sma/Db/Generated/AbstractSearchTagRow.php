<?php
namespace Sma\Db\Generated;

use Osf\Db\Row\AbstractRowGateway;

/**
 * Row gateway for search_tag
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use SearchTagRow instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractSearchTagRow extends AbstractRowGateway
{

    protected $schemaKey = 'common';

    protected $table = 'search_tag';

    protected $primaryKeyColumn = [
        'id',
    ];

    final public function getId()
    {
        return $this->get('id');
    }

    /**
     * @return \Sma\Db\SearchTagRow
     */
    final public function setId($value)
    {
        return $this->set('id', $value);
    }

    final public function getIdSearch()
    {
        return $this->get('id_search');
    }

    /**
     * @return \Sma\Db\SearchTagRow
     */
    final public function setIdSearch($value)
    {
        return $this->set('id_search', $value);
    }

    final public function getTag()
    {
        return $this->get('tag');
    }

    /**
     * @return \Sma\Db\SearchTagRow
     */
    final public function setTag($value)
    {
        return $this->set('tag', $value);
    }

    final public function getIdAccount()
    {
        return $this->get('id_account');
    }

    /**
     * @return \Sma\Db\SearchTagRow
     */
    final public function setIdAccount($value)
    {
        return $this->set('id_account', $value);
    }
}