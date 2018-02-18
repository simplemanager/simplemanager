<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for search
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use SearchTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractSearchTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_TITLE = 'title';
    const COL_DOC = 'doc';
    const COL_DOC_UID = 'doc_uid';
    const COL_SEARCH_CONTENT = 'search_content';
    const COL_LEVEL = 'level';
    const COL_URL = 'url';
    const COL_PARAMS = 'params';
    const COL_DATE_INSERT = 'date_insert';
    const COL_ID_ACCOUNT = 'id_account';

    protected $schemaKey = 'common';

    protected $table = 'search';

    protected $rowClass = '\\Sma\\Db\\SearchRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => 'Titre original',
        ],
        'doc' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'Document original',
        ],
        'doc_uid' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'search_content' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'contenu utilise pour la recherche',
        ],
        'level' => [
            'isNullable' => false,
            'dataType' => 'tinyint',
            'characterMaximumLength' => null,
            'comment' => 'Priorité 0 = faible, 5 = fort',
        ],
        'url' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => null,
        ],
        'params' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'date_insert' => [
            'isNullable' => false,
            'dataType' => 'timestamp',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_account' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_search_PRIMARY' => [
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
     * @return \Sma\Db\SearchRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}