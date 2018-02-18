<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for company
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use CompanyTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractCompanyTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_UID = 'uid';
    const COL_HASH = 'hash';
    const COL_TYPE = 'type';
    const COL_LEGAL_STATUS = 'legal_status';
    const COL_TITLE = 'title';
    const COL_TEL = 'tel';
    const COL_FAX = 'fax';
    const COL_EMAIL = 'email';
    const COL_TVA_INTRA = 'tva_intra';
    const COL_CHARGE_WITH_TAX = 'charge_with_tax';
    const COL_DESCRIPTION = 'description';
    const COL_ID_ADDRESS = 'id_address';
    const COL_ID_ADDRESS_DELIVERY = 'id_address_delivery';
    const COL_ID_CONTACT = 'id_contact';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_URL = 'url';
    const COL_STATUS = 'status';
    const COL_ID_LOGO = 'id_logo';
    const COL_ID_COMPANY = 'id_company';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'common';

    protected $table = 'company';

    protected $rowClass = '\\Sma\\Db\\CompanyRow';

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
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'hash' => [
            'isNullable' => true,
            'dataType' => 'char',
            'characterMaximumLength' => 64,
            'comment' => null,
        ],
        'type' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 14,
            'comment' => 'label: Type
element: select
options:
  client: Client
  prospect: Prospect
  branch: Associé
  competitor: Concurrent
  administration: Administration
  other: Autre
default: client',
            'permitted_values' => [
                'client',
                'prospect',
                'mine',
                'branch',
                'competitor',
                'administration',
                'other',
            ],
        ],
        'legal_status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 4,
            'comment' => 'label: Statut Juridique
element: select
options:
  a: Association loi 1901
  ae: Auto Entrepreneur
  ei: "Entreprise Individuelle / Micro Entreprise"
  eurl: EURL (Entreprise Unipersonnelle à Responsabilité Limitée)
  sarl: SARL (Société à Responsabilité Limitée)
  sa: SA (Société Anonyme)
  sas: SAS (Société par Actions Simplifiée)
  sasu: SASU (Société par Actions Simplifiée Unipersonnelle)
  sci: SCI (Société Civile Immobilière)
  scp: SCP (Société Civile Professionnelle)
  scm: SCM (Société Civile de Moyens)
default: \'?\'',
            'permitted_values' => [
                '?',
                'a',
                'ae',
                'ei',
                'eurl',
                'sarl',
                'sa',
                'sas',
                'sasu',
                'sci',
                'scp',
                'scm',
            ],
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => 'label: Société
element: input
placeholder: Nom de la société
left:
  icon: industry',
        ],
        'tel' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => 'type: tel
element: input
label: Tel
placeholder: Téléphone
left:
  icon: phone
size: 6',
        ],
        'fax' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => 'type: tel
element: input
label: Fax
placeholder: Fax
left:
  icon: fax
size: 6',
        ],
        'email' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'type: email
element: input
label: Email
placeholder: "E-mail de contact (ex: contact@monentreprise.com)"
left:
  icon: at',
        ],
        'tva_intra' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 30,
            'comment' => 'element: input
label: TVA Intracommunautaire
filters: 
  CleanPhrase:
  StringToUpper:
validators:
  TvaIntra:
left:
  icon: money',
        ],
        'charge_with_tax' => [
            'isNullable' => false,
            'dataType' => 'tinyint',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'element: input
label: Description
placeholder: "Description ou slogan (ex: Le numéro 1 de la gestion)"
left:
  icon: bullhorn',
        ],
        'id_address' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_address_delivery' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_contact' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
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
        'url' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'type: url
element: input
label: Url
placeholder: "Site web (ex: http://monentreprise.com)"
left:
  icon: desktop',
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 8,
            'comment' => 'default: enabled',
            'permitted_values' => [
                'enabled',
                'disabled',
            ],
        ],
        'id_logo' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_company' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'Entreprise cliente',
        ],
        'bean' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        '_zf_company_PRIMARY' => [
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
        '_zf_company_hash' => [
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
        'company_ibfk_1' => [
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
        'company_ibfk_4' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'image',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'RESTRICT',
            'check' => null,
        ],
        'company_ibfk_5' => [
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
        'company_ibfk_6' => [
            'type' => 'FOREIGN KEY',
            'referenced' => [
                'schema' => DB_SCHEMAS['common'],
                'table' => 'contact',
                'columns' => [
                    'id',
                ],
            ],
            'match' => 'NONE',
            'update' => 'CASCADE',
            'delete' => 'SET NULL',
            'check' => null,
        ],
        'company_ibfk_7' => [
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
    ];

    protected $triggers = [
        'company_date_insert' => [
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
        'company_date_update' => [
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
     * @return \Sma\Db\CompanyRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}