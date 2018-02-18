<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for ticket
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use TicketTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractTicketTable extends AbstractTableGateway
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

    protected $schemaKey = 'common';

    protected $table = 'ticket';

    protected $rowClass = '\\Sma\\Db\\TicketRow';

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
    ];

    protected $constraints = [
        '_zf_ticket_PRIMARY' => [
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
    ];

    protected $triggers = [
        'ticket_date_insert' => [
            'created' => 1506533675,
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
        'ticket_date_update' => [
            'created' => 1506533675,
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
     * @return \Sma\Db\TicketRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}