<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for form_stats
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use FormStatsTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractFormStatsTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_CLASS = 'class';
    const COL_FORM_VALUES = 'form_values';

    protected $schemaKey = 'common';

    protected $table = 'form_stats';

    protected $rowClass = '\\Sma\\Db\\FormStatsRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'class' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 100,
            'comment' => null,
        ],
        'form_values' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_form_stats_PRIMARY' => [
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
        
    ];

    /**
     * @return \Sma\Db\FormStatsRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}