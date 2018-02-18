<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for sequence
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use SequenceTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractSequenceTable extends AbstractTableGateway
{
    const COL_ID_ACCOUNT = 'id_account';
    const COL_NAME = 'name';
    const COL_VALUE = 'value';

    protected $schemaKey = 'common';

    protected $table = 'sequence';

    protected $rowClass = '\\Sma\\Db\\SequenceRow';

    protected $fields = [
        'id_account' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'name' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 20,
            'comment' => null,
        ],
        'value' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_sequence_PRIMARY' => [
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
        'sequence_ibfk_1' => [
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
            'delete' => 'NO ACTION',
            'check' => null,
        ],
    ];

    protected $triggers = [
        
    ];

    /**
     * @return \Sma\Db\SequenceRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}