<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for document_history
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentHistoryTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentHistoryTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_DOCUMENT = 'id_document';
    const COL_DUMP = 'dump';
    const COL_SOURCE = 'source';
    const COL_HASH = 'hash';
    const COL_DATE_INSERT = 'date_insert';
    const COL_ID_ACCOUNT = 'id_account';

    protected $schemaKey = 'common';

    protected $table = 'document_history';

    protected $rowClass = '\\Sma\\Db\\DocumentHistoryRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_document' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'dump' => [
            'isNullable' => false,
            'dataType' => 'mediumblob',
            'characterMaximumLength' => 16777215,
            'comment' => null,
        ],
        'source' => [
            'isNullable' => true,
            'dataType' => 'mediumtext',
            'characterMaximumLength' => 16777215,
            'comment' => null,
        ],
        'hash' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 64,
            'comment' => null,
        ],
        'date_insert' => [
            'isNullable' => false,
            'dataType' => 'datetime',
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
        '_zf_document_history_PRIMARY' => [
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
        'document_history_ibfk_1' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'document',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'CASCADE',
            'check' => null,
        ],
        'document_history_ibfk_3' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['admin'],
                'table' => 'account',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'CASCADE',
            'check' => null,
        ],
    ];

    protected $triggers = [
        'document_history_date_insert' => [
            'created' => 1506533674,
            'event' => [
                'manipulation' => 'INSERT',
                'catalog' => 'def',
            ],
            'action' => [
                'condition' => null,
                'order' => '1',
                'orientation' => 'ROW',
                'statement' => 'SET NEW.date_insert=NOW()',
                'timing' => 'BEFORE',
                'referenceNewRow' => 'NEW',
                'referenceNewTable' => null,
                'referenceOldRow' => 'OLD',
                'referenceOldTable' => null,
            ],
        ],
    ];

    /**
     * @return \Sma\Db\DocumentHistoryRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}