<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for account
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use AccountTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractAccountTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_EMAIL = 'email';
    const COL_PASSWORD = 'password';
    const COL_FIRSTNAME = 'firstname';
    const COL_LASTNAME = 'lastname';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_STATUS = 'status';
    const COL_ID_CAMPAIGN = 'id_campaign';
    const COL_COMMENT = 'comment';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'admin';

    protected $table = 'account';

    protected $rowClass = '\\Sma\\Db\\AccountRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'email' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'type: email
label: E-mail
placeholder: mon@email.com
left:
  icon: at',
        ],
        'password' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 64,
            'comment' => 'type: password
label: Mot de passe
placeholder: mot de passe
validators:
  Password:
left: 
  icon: key',
        ],
        'firstname' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'label: Prénom
filters: 
  CleanPhrase:
  UcPhrase:
validators:
  len: 
    min: 2
left: 
  icon: user
size: 6',
        ],
        'lastname' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'label: Nom
filters: 
  CleanPhrase:
  UcPhrase:
validators:
  len: 2
left: 
  icon: user
size: 6',
        ],
        'date_insert' => [
            'isNullable' => false,
            'dataType' => 'datetime',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'date_update' => [
            'isNullable' => false,
            'dataType' => 'datetime',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 9,
            'comment' => 'label: Statut
desc: Etat du compte
acl: ADMIN
left:
  icon: check
size: 6',
            'permitted_values' => [
                'draft',
                'enabled',
                'disabled',
                'suspended',
            ],
        ],
        'id_campaign' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'comment' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'label: Commentaires
acl: ADMIN',
        ],
        'bean' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'type: ignored',
        ],
    ];

    protected $constraints = [
        '_zf_account_PRIMARY' => [
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
        '_zf_account_email' => [
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
        'account_ibfk_1' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['admin'],
                'table' => 'campaign',
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
        'account_date_insert' => [
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
        'account_date_update' => [
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
     * @return \Sma\Db\AccountRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}