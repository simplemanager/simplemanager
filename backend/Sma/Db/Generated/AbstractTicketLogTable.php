<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for ticket_log
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use TicketLogTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractTicketLogTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_TICKET = 'id_ticket';
    const COL_COMMENT = 'comment';
    const COL_DATE_INSERT = 'date_insert';
    const COL_ID_ACCOUNT = 'id_account';

    protected $schemaKey = 'common';

    protected $table = 'ticket_log';

    protected $rowClass = '\\Sma\\Db\\TicketLogRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_ticket' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'comment' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'element: input
label: Commentaire
required: 1
acl: ADMIN',
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
        '_zf_ticket_log_PRIMARY' => [
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
        'ticket_log_ibfk_1' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'ticket',
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
        'ticket_log_date_insert' => [
            'created' => 1506533675,
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
     * @return \Sma\Db\TicketLogRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}