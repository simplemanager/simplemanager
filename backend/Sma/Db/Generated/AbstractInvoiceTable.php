<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for invoice
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use InvoiceTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractInvoiceTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_UID = 'uid';
    const COL_ID_PROVIDER = 'id_provider';
    const COL_ID_RECIPIENT = 'id_recipient';
    const COL_ID_DOCUMENT = 'id_document';
    const COL_ID_DOCUMENT_HISTORY = 'id_document_history';
    const COL_CODE = 'code';
    const COL_PRODUCT_COUNT = 'product_count';
    const COL_TOTAL_HT = 'total_ht';
    const COL_TOTAL_TTC = 'total_ttc';
    const COL_DESCRIPTION = 'description';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'common';

    protected $table = 'invoice';

    protected $rowClass = '\\Sma\\Db\\InvoiceRow';

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
        'uid' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_provider' => [
            'isNullable' => true,
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
        'id_document' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_document_history' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'code' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => null,
        ],
        'product_count' => [
            'isNullable' => false,
            'dataType' => 'smallint',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'total_ht' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'total_ttc' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
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
        'bean' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_invoice_PRIMARY' => [
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
        '_zf_invoice_id_document' => [
            'type' => 'UNIQUE',
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
        '_zf_invoice_id_document_history' => [
            'type' => 'UNIQUE',
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
        'invoice_ibfk_1' => [
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
        'invoice_ibfk_2' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'company',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'RESTRICT',
            'check' => null,
        ],
        'invoice_ibfk_3' => [
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
        'invoice_ibfk_4' => [
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
            'delete' => 'RESTRICT',
            'check' => null,
        ],
        'invoice_ibfk_5' => [
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
            'delete' => 'RESTRICT',
            'check' => null,
        ],
    ];

    protected $triggers = [
        'order_date_insert' => [
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
        'order_date_update' => [
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
     * @return \Sma\Db\InvoiceRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}