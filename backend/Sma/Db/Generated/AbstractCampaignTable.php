<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for campaign
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use CampaignTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractCampaignTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_CODE = 'code';
    const COL_PROVIDER_NAME = 'provider_name';
    const COL_PROVIDER_URL = 'provider_url';
    const COL_PROVIDER_ID_COMPANY = 'provider_id_company';
    const COL_ACCESS_COUNT = 'access_count';
    const COL_COST = 'cost';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';

    protected $schemaKey = 'admin';

    protected $table = 'campaign';

    protected $rowClass = '\\Sma\\Db\\CampaignRow';

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
            'characterMaximumLength' => 10,
            'comment' => null,
        ],
        'provider_name' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => null,
        ],
        'provider_url' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 250,
            'comment' => null,
        ],
        'provider_id_company' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'access_count' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'cost' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'date_insert' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'date_update' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_campaign_PRIMARY' => [
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
        'campaign_ibfk_1' => [
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
            'delete' => 'SET NULL',
            'check' => null,
        ],
    ];

    protected $triggers = [
        'campaign_insert' => [
            'created' => 1509806436,
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
        'campaign_update' => [
            'created' => 1509806436,
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
     * @return \Sma\Db\CampaignRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}