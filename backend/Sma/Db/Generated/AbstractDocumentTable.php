<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for document
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_ID_RECIPIENT = 'id_recipient';
    const COL_UID = 'uid';
    const COL_TYPE = 'type';
    const COL_STATUS = 'status';
    const COL_TEMPLATE = 'template';
    const COL_TITLE = 'title';
    const COL_SUBJECT = 'subject';
    const COL_DESCRIPTION = 'description';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';

    protected $schemaKey = 'common';

    protected $table = 'document';

    protected $rowClass = '\\Sma\\Db\\DocumentRow';

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
        'id_recipient' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'uid' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'type' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 7,
            'comment' => null,
            'permitted_values' => [
                'letter',
                'quote',
                'order',
                'invoice',
                'form',
            ],
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 9,
            'comment' => null,
            'permitted_values' => [
                'created',
                'sent',
                'read',
                'processed',
                'canceled',
            ],
        ],
        'template' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => null,
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 256,
            'comment' => null,
        ],
        'subject' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 256,
            'comment' => null,
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'date_insert' => [
            'isNullable' => false,
            'dataType' => 'datetime',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'date_update' => [
            'isNullable' => false,
            'dataType' => 'datetime',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_document_PRIMARY' => [
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
        'document_ibfk_1' => [
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
        'document_ibfk_2' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'contact',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'SET NULL',
            'check' => null,
        ],
    ];

    protected $triggers = [
        'document_date_insert' => [
            'created' => 1506533674,
            'event' => [
                'manipulation' => 'INSERT',
                'catalog' => 'def',
            ],
            'action' => [
                'condition' => null,
                'order' => '1',
                'orientation' => 'ROW',
                'statement' => 'BEGIN
  SET NEW.date_insert=NOW();
  SET NEW.date_update=NOW();
END',
                'timing' => 'BEFORE',
                'referenceNewRow' => 'NEW',
                'referenceNewTable' => null,
                'referenceOldRow' => 'OLD',
                'referenceOldTable' => null,
            ],
        ],
        'document_date_update' => [
            'created' => 1506533674,
            'event' => [
                'manipulation' => 'UPDATE',
                'catalog' => 'def',
            ],
            'action' => [
                'condition' => null,
                'order' => '1',
                'orientation' => 'ROW',
                'statement' => 'SET NEW.date_update=NOW()',
                'timing' => 'BEFORE',
                'referenceNewRow' => 'NEW',
                'referenceNewTable' => null,
                'referenceOldRow' => 'OLD',
                'referenceOldTable' => null,
            ],
        ],
    ];

    /**
     * @return \Sma\Db\DocumentRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}