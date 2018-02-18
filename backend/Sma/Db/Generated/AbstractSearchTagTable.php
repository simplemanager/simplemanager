<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for search_tag
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use SearchTagTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractSearchTagTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_SEARCH = 'id_search';
    const COL_TAG = 'tag';
    const COL_ID_ACCOUNT = 'id_account';

    protected $schemaKey = 'common';

    protected $table = 'search_tag';

    protected $rowClass = '\\Sma\\Db\\SearchTagRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_search' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'tag' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 20,
            'comment' => 'filtre de recherche. Ex: c12 (contact 12) ou d45 (document 45)',
        ],
        'id_account' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_search_tag_PRIMARY' => [
            'type' => 'PRIMARY KEY',
            'referenced' => [
                'schema' => null,
                'table' => null,
                'columns' => null,
            ],
            'match' => null,
            'update' => null,
            'delete' => null,
            'check' => null,
        ],
    ];

    protected $triggers = [
        
    ];

    /**
     * @return \Sma\Db\SearchTagRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}