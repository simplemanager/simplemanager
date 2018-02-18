<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for letter_template
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use LetterTemplateTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractLetterTemplateTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_CATEGORY = 'category';
    const COL_TITLE = 'title';
    const COL_DESCRIPTION = 'description';
    const COL_DATA_TYPE = 'data_type';
    const COL_DATA_TYPE_FILTERS = 'data_type_filters';
    const COL_TARGET_TYPE = 'target_type';
    const COL_SEARCH_DATA = 'search_data';
    const COL_BEAN = 'bean';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';

    protected $schemaKey = 'common';

    protected $table = 'letter_template';

    protected $rowClass = '\\Sma\\Db\\LetterTemplateRow';

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
        'category' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 100,
            'comment' => null,
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 250,
            'comment' => 'element: input
label: Titre du modèle
required: 1',
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'element: textarea
label: Description succincte de ce modèle',
        ],
        'data_type' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 9,
            'comment' => null,
            'permitted_values' => [
                'recipient',
                'invoices',
                'invoice',
                'order',
                'quote',
            ],
        ],
        'data_type_filters' => [
            'isNullable' => true,
            'dataType' => 'set',
            'characterMaximumLength' => 87,
            'comment' => null,
            'permitted_values' => [
                'status_created',
                'status_sent',
                'status_read',
                'status_processed',
                'status_canceled',
                'overdue',
                'on_time',
            ],
        ],
        'target_type' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 6,
            'comment' => null,
            'permitted_values' => [
                'email',
                'letter',
                'both',
            ],
        ],
        'search_data' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'bean' => [
            'isNullable' => false,
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
        '_zf_letter_template_PRIMARY' => [
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
        'letter_template_ibfk_1' => [
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
        'letter_template_date_insert' => [
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
        'letter_template_date_update' => [
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
     * @return \Sma\Db\LetterTemplateRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}