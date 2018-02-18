<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for formula
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use FormulaTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractFormulaTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_APPLICATION = 'id_application';
    const COL_NAME = 'name';
    const COL_DESCRIPTION = 'description';
    const COL_DURATION = 'duration';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'admin';

    protected $table = 'formula';

    protected $rowClass = '\\Sma\\Db\\FormulaRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_application' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'name' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => 'label: Nom',
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'label: Description',
        ],
        'duration' => [
            'isNullable' => false,
            'dataType' => 'smallint',
            'characterMaximumLength' => null,
            'comment' => 'label: Durée
placeholder: \'[nombre de jours]\'',
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
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_formula_PRIMARY' => [
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
        'product_date_insert' => [
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
        'product_date_update' => [
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
     * @return \Sma\Db\FormulaRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}