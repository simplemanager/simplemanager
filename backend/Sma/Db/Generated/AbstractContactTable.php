<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for contact
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use ContactTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractContactTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_CIVILITY = 'civility';
    const COL_FIRSTNAME = 'firstname';
    const COL_LASTNAME = 'lastname';
    const COL_FUNCTION = 'function';
    const COL_EMAIL = 'email';
    const COL_TEL = 'tel';
    const COL_FAX = 'fax';
    const COL_GSM = 'gsm';
    const COL_ID_ADDRESS = 'id_address';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_COMMENT = 'comment';
    const COL_ID_COMPANY = 'id_company';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_IS_ACCOUNT = 'is_account';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'common';

    protected $table = 'contact';

    protected $rowClass = '\\Sma\\Db\\ContactRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'civility' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 5,
            'comment' => 'element: select
left:
  icon: transgender
options:
  M.: M.
  Mme: Mme
#  Mlle: Mlle
#  Dr: Dr
#  Me: Me
#  Pr: Pr
size: 3',
        ],
        'firstname' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'label: Prénom
filters:
  CleanPhrase:
  UcPhrase:
left:
  icon: user
size: 5',
        ],
        'lastname' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'label: Nom
filters:
  CleanPhrase:
  UcPhrase:
placeholder: Nom de famille
size: 4',
        ],
        'function' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 80,
            'comment' => 'label: Fonction
placeholder: Ma fonction au sein de l\'entreprise
filters:
  trim:
left:
  icon: id-card-o',
        ],
        'email' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => 'label: E-mail
placeholder: nom@domaine.com
type: email',
        ],
        'tel' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => 'type: tel
label: Téléphone fixe
left:
  icon: phone
size: 4',
        ],
        'fax' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => 'type: tel
label: Fax
left:
  icon: fax
size: 4',
        ],
        'gsm' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => 'type: tel
label: Mobile
left:
  icon: mobile
size: 4',
        ],
        'id_address' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'element: select',
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
        'comment' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'id_company' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'id_account' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'is_account' => [
            'isNullable' => true,
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
        '_zf_contact_PRIMARY' => [
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
        '_zf_contact_is_account' => [
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
        'contact_account_fk' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['admin'],
                'table' => 'account',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'RESTRICT',
            'delete' => 'RESTRICT',
            'check' => null,
        ],
        'contact_address_fk' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'address',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'SET NULL',
            'check' => null,
        ],
        'contact_ibfk_1' => [
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
        'contact_date_insert' => [
            'created' => 1506533673,
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
        'contact_date_update' => [
            'created' => 1506533673,
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
     * @return \Sma\Db\ContactRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}