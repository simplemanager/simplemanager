<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for product
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use ProductTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractProductTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_UID = 'uid';
    const COL_TITLE = 'title';
    const COL_PRICE = 'price';
    const COL_CODE = 'code';
    const COL_PRICE_TYPE = 'price_type';
    const COL_TAX = 'tax';
    const COL_UNIT = 'unit';
    const COL_DISCOUNT = 'discount';
    const COL_STATUS = 'status';
    const COL_DESCRIPTION = 'description';
    const COL_BEAN_TYPE = 'bean_type';
    const COL_BEAN = 'bean';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';

    protected $schemaKey = 'common';

    protected $table = 'product';

    protected $rowClass = '\\Sma\\Db\\ProductRow';

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
        'uid' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'label: Nom
filters:
  trim:
attrs:
  autofocus:
size: 6',
        ],
        'price' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => 'label: Prix
type: price
validators:
  Currency:
filters:
  Currency:
  RemoveSpaces:
left:
  icon: eur
size: 3
tooltip: Prix Unitaire',
        ],
        'code' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 16,
            'comment' => 'Label: Code
filters:
  trim:
tooltip: "Code produit : laisser vide pour générer automatiquement"
size: 3',
        ],
        'price_type' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 3,
            'comment' => 'label: Prix mentionné
element: select
options:
  ht: HT (sans taxe)
  ttc: TTC (avec taxe)
left:
  icon: balance-scale
default: ht
size: 6
relevance: low
tooltip: Le prix mentionné est...',
            'permitted_values' => [
                'ht',
                'ttc',
            ],
        ],
        'tax' => [
            'isNullable' => true,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => 'label: TVA
element: select
options:
  20: "20% (normal)"
  10: "10% (réduit)"
  5.5: "5.5% (réduit)"
  2.1: "2.1% (réduit)"
  0: "Aucune"
left:
  icon: institution
default: 20
size: 6
relevance: low
tooltip: TVA',
        ],
        'unit' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 3,
            'comment' => 'element: select
label: Unité
options:
  h: heure
  d: jour
  l: litre
  g: g
  kg: kg
  t: tonne
size: 4
tooltip: Unité de tarif
relevance: low',
        ],
        'discount' => [
            'isNullable' => true,
            'dataType' => 'tinyint',
            'characterMaximumLength' => null,
            'comment' => 'label: Remise
type: number
validators:
  Percentage:
filters:
  Percentage:
  RemoveSpaces:
left:
  icon: star-o
right:
  icon: percent
size: 4
relevance: low
attrs:
  step: 1
tooltip: Remise (pourcentage)',
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'tinyint',
            'characterMaximumLength' => null,
            'comment' => 'element: select
label: Etat
options:
  1: Activé
  0: Désactivé
left:
  icon: power-off
size: 4
default: 1
relevance: low
tooltip: Désactiver un produit le rend indisponible',
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'label: Description
placeholder: "Description succincte"
filters:
  trim:
relevance: low',
        ],
        'bean_type' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => 'type: ignored',
        ],
        'bean' => [
            'isNullable' => true,
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
        '_zf_product_PRIMARY' => [
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
        '_zf_product_code' => [
            'type' => 'UNIQUE',
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
        'product_account_fk' => [
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
            'delete' => 'CASCADE',
            'check' => null,
        ],
    ];

    protected $triggers = [
        'product_date_insert' => [
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
        'product_date_update' => [
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
     * @return \Sma\Db\ProductRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}