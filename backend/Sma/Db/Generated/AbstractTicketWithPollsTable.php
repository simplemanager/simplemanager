<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for ticket_with_polls
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use TicketWithPollsTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractTicketWithPollsTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_CATEGORY = 'category';
    const COL_TITLE = 'title';
    const COL_CONTENT = 'content';
    const COL_RESPONSE = 'response';
    const COL_STATUS = 'status';
    const COL_VISIBILITY = 'visibility';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_UPDATE = 'date_update';
    const COL_POLL_COUNT = 'poll_count';

    protected $schemaKey = 'common';

    protected $table = 'ticket_with_polls';

    protected $rowClass = '\\Sma\\Db\\TicketWithPollsRow';

    protected $fields = [
        'id' => [
            'isNullable' => false,
            'dataType' => 'int',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'category' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 11,
            'comment' => 'label: Type de demande
element: select
options:
  bug: Disfonctionnement (bug)
  doc: Problème de compréhension (demande de documentation)
  improvement: Demande d\'amélioration d\'une fonctionnalité existante
  feature: Demande de nouvelle fonctionnalité
default: bug
desc: Le type de demande détermine sa priorité. Veillez à faire un choix pertinent.',
            'permitted_values' => [
                'bug',
                'doc',
                'improvement',
                'feature',
            ],
        ],
        'title' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => 'element: input
label: Titre
placeholder: Proposez un titre court et explicite
required: 1
validators: 
  len: 6
filters:
  StringTrim:',
        ],
        'content' => [
            'isNullable' => false,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'element: textarea
label: Description
placeholder: Décrivez ici votre demande avec un maximum de précision et de clarté.
required: 1
validators: 
  len: 10
filters:
  StringTrim:',
        ],
        'response' => [
            'isNullable' => true,
            'dataType' => 'text',
            'characterMaximumLength' => 65535,
            'comment' => 'element: textarea
label: Réponse
required: 0
filters:
  StringTrim:',
        ],
        'status' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 11,
            'comment' => 'label: Etat (status)
element: select
options:
  draft: Brouillon
  under_study: En cours d\'étude
  accepted: Accepté
  in_progress: En travaux
  test: En test
  beta: Version beta
  published: Publié (réalisé)
  closed: Fermé
  refused: Refusé (annulé)
  deleted: Supprimé
default: draft',
            'permitted_values' => [
                'draft',
                'under_study',
                'accepted',
                'in_progress',
                'test',
                'beta',
                'published',
                'closed',
                'refused',
                'deleted',
            ],
        ],
        'visibility' => [
            'isNullable' => false,
            'dataType' => 'enum',
            'characterMaximumLength' => 6,
            'comment' => 'label: Visibilité
element: select
options:
  public: Publique (tous les utilisateurs)
  admin: Administrateurs uniquement
default: public',
            'permitted_values' => [
                'public',
                'admin',
            ],
        ],
        'id_account' => [
            'isNullable' => false,
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
        'poll_count' => [
            'isNullable' => false,
            'dataType' => 'bigint',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
    ];

    protected $constraints = [
        
    ];

    protected $triggers = [
        
    ];

    protected $view = [
        'definition' => 'select `' . DB_SCHEMAS['common'] . '`.`ticket`.`id` AS `id`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`category` AS `category`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`title` AS `title`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`content` AS `content`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`response` AS `response`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`status` AS `status`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`visibility` AS `visibility`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`id_account` AS `id_account`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`date_insert` AS `date_insert`,`' . DB_SCHEMAS['common'] . '`.`ticket`.`date_update` AS `date_update`,count(`' . DB_SCHEMAS['common'] . '`.`ticket_poll`.`id_account`) AS `poll_count` from (`' . DB_SCHEMAS['common'] . '`.`ticket` left join `' . DB_SCHEMAS['common'] . '`.`ticket_poll` on(((`' . DB_SCHEMAS['common'] . '`.`ticket`.`id` = `' . DB_SCHEMAS['common'] . '`.`ticket_poll`.`id_ticket`) and `' . DB_SCHEMAS['common'] . '`.`ticket_poll`.`id_account` in (select `' . DB_SCHEMAS['admin'] . '`.`account`.`id` from `' . DB_SCHEMAS['admin'] . '`.`account` where ((`' . DB_SCHEMAS['admin'] . '`.`account`.`id` = `' . DB_SCHEMAS['common'] . '`.`ticket_poll`.`id_account`) and (`' . DB_SCHEMAS['admin'] . '`.`account`.`status` = \'enabled\')))))) group by `' . DB_SCHEMAS['common'] . '`.`ticket`.`id`',
        'check' => 'NONE',
        'updatable' => false,
    ];

    /**
     * @return \Sma\Db\TicketWithPollsRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}