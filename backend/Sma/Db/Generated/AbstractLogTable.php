<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for log
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use LogTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractLogTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_LEVEL = 'level';
    const COL_MESSAGE = 'message';
    const COL_PAGE = 'page';
    const COL_IP = 'ip';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_CATEGORY = 'category';
    const COL_PAGE_INFO = 'page_info';
    const COL_DUMP = 'dump';

    protected $schemaKey = 'common';

    protected $table = 'log';

    protected $rowClass = '\\Sma\\Db\\LogRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'level' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 7,
            'comment' => null,
            'permitted_values' => [
                'error',
                'warning',
                'info',
            ],
        ],
        'message' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => null,
        ],
        'page' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => null,
        ],
        'ip' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 45,
            'comment' => null,
        ],
        'id_account' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
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
        'category' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 20,
            'comment' => null,
        ],
        'page_info' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'dump' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_log_PRIMARY' => [
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
        'log_date_update_insert' => [
            'created' => 1506533674,
            'event' => [
                'manipulation' => 'INSERT',
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
        'log_date_update' => [
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
     * @return \Sma\Db\LogRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}