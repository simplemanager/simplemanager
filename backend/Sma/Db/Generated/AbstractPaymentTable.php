<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for payment
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use PaymentTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractPaymentTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_AMOUNT = 'amount';
    const COL_DATE_BEGIN = 'date_begin';
    const COL_DATE_END = 'date_end';
    const COL_ID_PRODUCT = 'id_product';
    const COL_STATUS = 'status';
    const COL_COMMENT = 'comment';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';

    protected $schemaKey = 'admin';

    protected $table = 'payment';

    protected $rowClass = '\\Sma\\Db\\PaymentRow';

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
        'amount' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'date_begin' => [
            'isNullable' => false,
            'dataType' => 'date',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'date_end' => [
            'isNullable' => false,
            'dataType' => 'date',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_product' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 9,
            'comment' => null,
            'permitted_values' => [
                'topay',
                'error',
                'enabled',
                'disabled',
                'cancelled',
            ],
        ],
        'comment' => [
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
        '_zf_payment_PRIMARY' => [
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
        'payment_account_fk' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['admin'],
                'table' => 'account',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'RESTRICT',
            'delete' => 'RESTRICT',
            'check' => null,
        ],
        'payment_formula_fk' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['admin'],
                'table' => 'formula',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'RESTRICT',
            'delete' => 'RESTRICT',
            'check' => null,
        ],
    ];

    protected $triggers = [
        'payment_date_insert' => [
            'created' => 1506533548,
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
        'payment_date_update' => [
            'created' => 1506533548,
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
     * @return \Sma\Db\PaymentRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}