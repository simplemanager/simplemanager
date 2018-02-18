<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for address
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use AddressTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractAddressTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ADDRESS = 'address';
    const COL_POSTAL_CODE = 'postal_code';
    const COL_CITY = 'city';
    const COL_COUNTRY = 'country';
    const COL_ID_ACCOUNT = 'id_account';

    protected $schemaKey = 'common';

    protected $table = 'address';

    protected $rowClass = '\\Sma\\Db\\AddressRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'address' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 250,
            'comment' => 'element: textarea
label: Adresse
filters: 
  StringTrim:
validators:
  PostalAddressBody:',
        ],
        'postal_code' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 8,
            'comment' => 'label: C. Postal
size: 3
filters:
  StringTrim:
validators:
  Alnum: 1',
        ],
        'city' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 80,
            'comment' => 'label: Ville
size: 9
filters:
  CleanPhrase:
  UcPhrase:',
        ],
        'country' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 80,
            'comment' => 'label: Pays
filters:
  CleanPhrase:
  UcPhrase:',
        ],
        'id_account' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
    ];

    protected $constraints = [
        '_zf_address_PRIMARY' => [
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
        'address_ibfk_1' => [
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
     * @return \Sma\Db\AddressRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}