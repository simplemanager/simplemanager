<?php
namespace Sma\Db\Generated;

use Osf\Db\Table\AbstractTableGateway;

/**
 * Table gateway for notification
 *
 * WARNING: This class is generated automatically, do not update it manually!
 *          Please use NotificationTable instead.
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 3.0.0
 * @package osf
 * @subpackage generated
 */
abstract class AbstractNotificationTable extends AbstractTableGateway
{
    const COL_ID = 'id';
    const COL_ID_ACCOUNT = 'id_account';
    const COL_DATE_INSERT = 'date_insert';
    const COL_DATE_END = 'date_end';
    const COL_ICON = 'icon';
    const COL_COLOR = 'color';
    const COL_CONTENT = 'content';
    const COL_LINK = 'link';

    protected $schemaKey = 'common';

    protected $table = 'notification';

    protected $rowClass = '\\Sma\\Db\\NotificationRow';

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
        'date_insert' => [
            'isNullable' => false,
            'dataType' => 'datetime',
            'characterMaximumLength' => null,
            'comment' => null,
        ],
        'date_end' => [
            'isNullable' => true,
            'dataType' => 'date',
            'characterMaximumLength' => null,
            'comment' => 'type: date
label: Fin de validité
size: 4',
        ],
        'icon' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 16,
            'comment' => 'element: select
placeholder: Icone
create: 1
options:
  info: info
  warning: warning
  bomb: bomb (error)
  check: check (success)
  bar-chart: bar-chart (resultats)
  bell-o: bell (notification)
  book: book (doc)
  bookmark: bookmark
  bug: bug
  calendar: calendar
  check-square-o: check-square (form/edit)
  circle-o: circle-o
  circle: circle
  coffee: coffee
  cog: cog
  comment: comment
  credit-card: credit-card
  envelope: envelope
  file-o: file-o (devis)
  file-text-o: file-text-o (commande)
  file-text: file-text (facture)
  film: film (vidéo)
  heart: heart (love)
  legal: legal (infos légales)
  link: link
  question: question (?)
  refresh: refresh
  thumbs-down: pouce bas (j\'aime pas)
  thumbs-up: pouce haut (j\'aime)
  paper-plane-o: send / sent
  user: user
size: 4',
        ],
        'color' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 16,
            'comment' => 'element: select
placeholder: Couleur
options:
  blue: Bleu
  orange: Orange
  red: Rouge
  green: Vert
  fuchsia: Fuchsia
  purple: Violet
  aqua: Aqua
  black: Noir
size: 4',
        ],
        'content' => [
            'isNullable' => false,
            'dataType' => 'varchar',
            'characterMaximumLength' => 255,
            'comment' => 'element: input
type: text
placeholder: Contenu
filters:
  StringTrim:',
        ],
        'link' => [
            'isNullable' => true,
            'dataType' => 'varchar',
            'characterMaximumLength' => 128,
            'comment' => 'element: input
type: text
placeholder: Lien
filters:
  StringTrim:',
        ],
    ];

    protected $constraints = [
        '_zf_notification_PRIMARY' => [
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
        'notification_ibfk_1' => [
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
        'notification_before_insert' => [
            'created' => 1508227446,
            'event' => [
                'manipulation' => 'INSERT',
                'catalog' => 'def',
            ],
            'action' => [
                'condition' => null,
                'order' => '1',
                'orientation' => 'ROW',
                'statement' => 'SET NEW.date_insert=NOW()',
                'timing' => 'BEFORE',
                'referenceNewRow' => 'NEW',
                'referenceNewTable' => null,
                'referenceOldRow' => 'OLD',
                'referenceOldTable' => null,
            ],
        ],
    ];

    /**
     * @return \Sma\Db\NotificationRow
     */
    public function find($id)
    {
        return parent::find($id);
    }
}