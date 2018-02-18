<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for document_event
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentEventTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentEventTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_ID_DOCUMENT = 'id_document';
    const COL_ID_DOCUMENT_HISTORY = 'id_document_history';
    const COL_EVENT = 'event';
    const COL_DATE = 'date';
    const COL_COMMENT = 'comment';

    protected $schemaKey = 'common';

    protected $table = 'document_event';

    protected $rowClass = '\\Sma\\Db\\DocumentEventRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_account' => [
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
        'id_document_history' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'event' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 16,
            'comment' => null,
            'permitted_values' => [
                'creation',
                'update',
                'sending',
                'read',
                'process',
                'delete',
                'status_created',
                'status_sent',
                'status_processed',
                'status_canceled',
            ],
        ],
        'date' => [
            'isNullable' => false,
            'dataType' => 'datetime',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'comment' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 250,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_document_event_PRIMARY' => [
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
        'document_event_ibfk_1' => [
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
            'delete' => 'RESTRICT',
            'check' => null,
        ],
        'document_event_ibfk_2' => [
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
        'document_event_ibfk_3' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'document_history',
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
        'letter_event_before_insert' => [
            'created' => 1506929574,
            'event' => [
                'manipulation' => 'INSERT',
                'catalog' => 'def',
            ],
            'action' => [
                'condition' => null,
                'order' => '1',
                'orientation' => 'ROW',
                'statement' => 'BEGIN
  SET NEW.date=NOW();
END',
                'timing' => 'BEFORE',
                'referenceNewRow' => 'NEW',
                'referenceNewTable' => null,
                'referenceOldRow' => 'OLD',
                'referenceOldTable' => null,
            ],
        ],
    ];

    /**
     * @return \Sma\Db\DocumentEventRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}