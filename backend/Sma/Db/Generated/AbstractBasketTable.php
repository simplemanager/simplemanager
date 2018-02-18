<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for basket
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use BasketTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractBasketTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_INVOICE = 'id_invoice';
    const COL_ID_PRODUCT = 'id_product';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_QUANTITY = 'quantity';
    const COL_DISCOUNT = 'discount';

    protected $schemaKey = 'common';

    protected $table = 'basket';

    protected $rowClass = '\\Sma\\Db\\BasketRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_invoice' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_product' => [
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
        'quantity' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'discount' => [
            'isNullable' => false,
            'dataType' => 'tinyint',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_basket_PRIMARY' => [
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
        'basket_ibfk_3' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'invoice',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'CASCADE',
            'check' => null,
        ],
        'basket_ibfk_4' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'product',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'CASCADE',
            'check' => null,
        ],
        'basket_ibfk_5' => [
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
     * @return \Sma\Db\BasketRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}