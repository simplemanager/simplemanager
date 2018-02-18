<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for address_contact
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use AddressContactTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractAddressContactTable extends AbstractTableGateway
{
    const COL_CIVILITY = 'civility';
    const COL_FIRSTNAME = 'firstname';
    const COL_LASTNAME = 'lastname';
    const COL_ADDRESS = 'address';
    const COL_POSTAL_CODE = 'postal_code';
    const COL_CITY = 'city';
    const COL_COUNTRY = 'country';
    const COL_EMAIL = 'email';
    const COL_TEL = 'tel';
    const COL_FAX = 'fax';
    const COL_GSM = 'gsm';
    const COL_FUNCTION = 'function';
    const COL_IS_ACCOUNT = 'is_account';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_ID_CONTACT = 'id_contact';
    const COL_ID_COMPANY = 'id_company';

    protected $schemaKey = 'common';

    protected $table = 'address_contact';

    protected $rowClass = '\\Sma\\Db\\AddressContactRow';

    protected $fields = [
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
        'address' => [
            'isNullable' => true,
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
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 80,
            'comment' => 'label: Ville
size: 9
filters:
  CleanPhrase:
  UcPhrase:',
        ],
        'country' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 80,
            'comment' => 'label: Pays
filters:
  CleanPhrase:
  UcPhrase:',
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
        'is_account' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_account' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
        'id_contact' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_company' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => 'type: ignored',
        ],
    ];

    protected $constraints = [
        
    ];

    protected $triggers = [
        
    ];

    protected $view = [
        'definition' => 'select `' . DB_SCHEMAS['common'] . '`.`contact`.`civility` AS `civility`,`' . DB_SCHEMAS['common'] . '`.`contact`.`firstname` AS `firstname`,`' . DB_SCHEMAS['common'] . '`.`contact`.`lastname` AS `lastname`,`' . DB_SCHEMAS['common'] . '`.`address`.`address` AS `address`,`' . DB_SCHEMAS['common'] . '`.`address`.`postal_code` AS `postal_code`,`' . DB_SCHEMAS['common'] . '`.`address`.`city` AS `city`,`' . DB_SCHEMAS['common'] . '`.`address`.`country` AS `country`,`' . DB_SCHEMAS['common'] . '`.`contact`.`email` AS `email`,`' . DB_SCHEMAS['common'] . '`.`contact`.`tel` AS `tel`,`' . DB_SCHEMAS['common'] . '`.`contact`.`fax` AS `fax`,`' . DB_SCHEMAS['common'] . '`.`contact`.`gsm` AS `gsm`,`' . DB_SCHEMAS['common'] . '`.`contact`.`function` AS `function`,`' . DB_SCHEMAS['common'] . '`.`contact`.`is_account` AS `is_account`,`' . DB_SCHEMAS['common'] . '`.`contact`.`id_account` AS `id_account`,`' . DB_SCHEMAS['common'] . '`.`contact`.`id` AS `id_contact`,`' . DB_SCHEMAS['common'] . '`.`contact`.`id_company` AS `id_company` from (`' . DB_SCHEMAS['common'] . '`.`contact` left join `' . DB_SCHEMAS['common'] . '`.`address` on((`' . DB_SCHEMAS['common'] . '`.`contact`.`id_address` = `' . DB_SCHEMAS['common'] . '`.`address`.`id`))) where (`' . DB_SCHEMAS['common'] . '`.`contact`.`is_account` is not null)',
        'check' => 'NONE',
        'updatable' => true,
    ];

    /**
     * @return \Sma\Db\AddressContactRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}