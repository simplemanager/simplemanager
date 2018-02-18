<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for image
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use ImageTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractImageTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_TYPE = 'type';
    const COL_CONTENT = 'content';
    const COL_COLOR = 'color';
    const COL_DESCRIPTION = 'description';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'common';

    protected $table = 'image';

    protected $rowClass = '\\Sma\\Db\\ImageRow';

    protected $fields = [
        'id' => [
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
                'logo',
                'unknown',
            ],
        ],
        'content' => [
            'isNullable' => false,
            'dataType' => 'mediumblob',
            'characterMaximumLength' => 16777215,
            'comment' => null,
        ],
        'color' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 6,
            'comment' => null,
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 250,
            'comment' => null,
        ],
        'id_account' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'bean' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_image_PRIMARY' => [
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
        'image_ibfk_1' => [
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
     * @return \Sma\Db\ImageRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}