<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for invoice_document
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use InvoiceDocumentTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractInvoiceDocumentTable extends AbstractTableGateway
{
    const COL_ID_INVOICE = 'id_invoice';
    const COL_ID_DOCUMENT = 'id_document';
    const COL_UID_INVOICE = 'uid_invoice';
    const COL_UID_DOCUMENT = 'uid_document';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_ID_PROVIDER = 'id_provider';
    const COL_ID_RECIPIENT = 'id_recipient';
    const COL_ID_DOCUMENT_HISTORY = 'id_document_history';
    const COL_CODE = 'code';
    const COL_PRODUCT_COUNT = 'product_count';
    const COL_TOTAL_HT = 'total_ht';
    const COL_TOTAL_TTC = 'total_ttc';
    const COL_DESCRIPTION = 'description';
    const COL_TYPE = 'type';
    const COL_STATUS = 'status';
    const COL_TITLE = 'title';
    const COL_SUBJECT = 'subject';
    const COL_DOCUMENT_DESCRIPTION = 'document_description';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_BEAN = 'bean';

    protected $schemaKey = 'common';

    protected $table = 'invoice_document';

    protected $rowClass = '\\Sma\\Db\\InvoiceDocumentRow';

    protected $fields = [
        'id_invoice' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_document' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'uid_invoice' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'uid_document' => [
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
        'id_provider' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_recipient' => [
            'isNullable' => true,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'id_document_history' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'code' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 32,
            'comment' => null,
        ],
        'product_count' => [
            'isNullable' => false,
            'dataType' => 'smallint',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'total_ht' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'total_ttc' => [
            'isNullable' => false,
            'dataType' => 'decimal',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'type' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 7,
            'comment' => null,
            'permitted_values' => [
                'letter',
                'quote',
                'order',
                'invoice',
                'form',
            ],
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 9,
            'comment' => null,
            'permitted_values' => [
                'created',
                'sent',
                'read',
                'processed',
                'canceled',
            ],
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 256,
            'comment' => null,
        ],
        'subject' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 256,
            'comment' => null,
        ],
        'document_description' => [
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
        'bean' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        
    ];

    protected $triggers = [
        
    ];

    protected $view = [
        'definition' => 'select `' . DB_SCHEMAS['common'] . '`.`invoice`.`id` AS `id_invoice`,`' . DB_SCHEMAS['common'] . '`.`document`.`id` AS `id_document`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`uid` AS `uid_invoice`,`' . DB_SCHEMAS['common'] . '`.`document`.`uid` AS `uid_document`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`id_account` AS `id_account`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`id_provider` AS `id_provider`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`id_recipient` AS `id_recipient`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`id_document_history` AS `id_document_history`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`code` AS `code`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`product_count` AS `product_count`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`total_ht` AS `total_ht`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`total_ttc` AS `total_ttc`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`description` AS `description`,`' . DB_SCHEMAS['common'] . '`.`document`.`type` AS `type`,`' . DB_SCHEMAS['common'] . '`.`document`.`status` AS `status`,`' . DB_SCHEMAS['common'] . '`.`document`.`title` AS `title`,`' . DB_SCHEMAS['common'] . '`.`document`.`subject` AS `subject`,`' . DB_SCHEMAS['common'] . '`.`document`.`description` AS `document_description`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`date_insert` AS `date_insert`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`date_update` AS `date_update`,`' . DB_SCHEMAS['common'] . '`.`invoice`.`bean` AS `bean` from `' . DB_SCHEMAS['common'] . '`.`invoice` join `' . DB_SCHEMAS['common'] . '`.`document` where (`' . DB_SCHEMAS['common'] . '`.`invoice`.`id_document` = `' . DB_SCHEMAS['common'] . '`.`document`.`id`)',
        'check' => 'NONE',
        'updatable' => true,
    ];

    /**
     * @return \Sma\Db\InvoiceDocumentRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}