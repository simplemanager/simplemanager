<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for document_history_current
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use DocumentHistoryCurrentTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractDocumentHistoryCurrentTable extends AbstractTableGateway
{
    const COL_ID_ACCOUNT = 'id_account';
    const COL_ID_DOCUMENT = 'id_document';
    const COL_ID_DOCUMENT_HISTORY = 'id_document_history';
    const COL_ID_RECIPIENT = 'id_recipient';
    const COL_UID = 'uid';
    const COL_TYPE = 'type';
    const COL_STATUS = 'status';
    const COL_TITLE = 'title';
    const COL_SUBJECT = 'subject';
    const COL_DESCRIPTION = 'description';
    const COL_TEMPLATE = 'template';
    const COL_SOURCE = 'source';
    const COL_DUMP = 'dump';
    const COL_HASH = 'hash';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';

    protected $schemaKey = 'common';

    protected $table = 'document_history_current';

    protected $rowClass = '\\Sma\\Db\\DocumentHistoryCurrentRow';

    protected $fields = [
        'id_account' => [
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
        'id_document_history' => [
            'isNullable' => false,
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
        'uid' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
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
        'description' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => null,
        ],
        'template' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => null,
        ],
        'source' => [
            'isNullable' => true,
            'dataType' => 'mediumtext',
            'characterMaximumLength' => 16777215,
            'comment' => null,
        ],
        'dump' => [
            'isNullable' => false,
            'dataType' => 'mediumblob',
            'characterMaximumLength' => 16777215,
            'comment' => null,
        ],
        'hash' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 64,
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
        
    ];

    protected $triggers = [
        
    ];

    protected $view = [
        'definition' => 'select `' . DB_SCHEMAS['common'] . '`.`document_history`.`id_account` AS `id_account`,`' . DB_SCHEMAS['common'] . '`.`document`.`id` AS `id_document`,`' . DB_SCHEMAS['common'] . '`.`document_history`.`id` AS `id_document_history`,`' . DB_SCHEMAS['common'] . '`.`document`.`id_recipient` AS `id_recipient`,`' . DB_SCHEMAS['common'] . '`.`document`.`uid` AS `uid`,`' . DB_SCHEMAS['common'] . '`.`document`.`type` AS `type`,`' . DB_SCHEMAS['common'] . '`.`document`.`status` AS `status`,`' . DB_SCHEMAS['common'] . '`.`document`.`title` AS `title`,`' . DB_SCHEMAS['common'] . '`.`document`.`subject` AS `subject`,`' . DB_SCHEMAS['common'] . '`.`document`.`description` AS `description`,`' . DB_SCHEMAS['common'] . '`.`document`.`template` AS `template`,`' . DB_SCHEMAS['common'] . '`.`document_history`.`source` AS `source`,`' . DB_SCHEMAS['common'] . '`.`document_history`.`dump` AS `dump`,`' . DB_SCHEMAS['common'] . '`.`document_history`.`hash` AS `hash`,`' . DB_SCHEMAS['common'] . '`.`document`.`date_insert` AS `date_insert`,`' . DB_SCHEMAS['common'] . '`.`document`.`date_update` AS `date_update` from `' . DB_SCHEMAS['common'] . '`.`document_history` join `' . DB_SCHEMAS['common'] . '`.`document` where ((`' . DB_SCHEMAS['common'] . '`.`document`.`id` = `' . DB_SCHEMAS['common'] . '`.`document_history`.`id_document`) and `' . DB_SCHEMAS['common'] . '`.`document_history`.`id` in (select max(`' . DB_SCHEMAS['common'] . '`.`document_history`.`id`) from `' . DB_SCHEMAS['common'] . '`.`document_history` join `' . DB_SCHEMAS['common'] . '`.`document` where (`' . DB_SCHEMAS['common'] . '`.`document`.`id` = `' . DB_SCHEMAS['common'] . '`.`document_history`.`id_document`) group by `' . DB_SCHEMAS['common'] . '`.`document`.`id`))',
        'check' => 'NONE',
        'updatable' => true,
    ];

    /**
     * @return \Sma\Db\DocumentHistoryCurrentRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}