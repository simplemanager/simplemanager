<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for event
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use EventTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractEventTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_CODE = 'code';
    const COL_DESCRIPTION = 'description';
    const COL_ID_ACCOUNT = 'id_account';

    protected $schemaKey = 'common';

    protected $table = 'event';

    protected $rowClass = '\\Sma\\Db\\EventRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'code' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => null,
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
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
        '_zf_event_PRIMARY' => [
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
        'event_ibfk_1' => [
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
    ];

    protected $triggers = [
        
    ];

    /**
     * @return \Sma\Db\EventRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}