<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractTicketTable;

/**
 * Table model for table ticket
 *
 * Use this class to complete AbstractTicketTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class TicketTable extends AbstractTicketTable
{
    public function getTypes()
    {
        return [
            'bug' => [
                'icon'   => 'bug', 
                'label'  => __("Disfonctionnement (bug)"),
                'status' => 'danger'
                ],
            'doc' => [
                'icon'   => 'book', 
                'label'  => __("Question (faq)"),
                'status' => 'info'
                ],
            'improvement' => [
                'icon'   => 'thumbs-o-up', 
                'label'  => __("Amélioration"),
                'status' => 'warning'
                ],
            'feature' => [
                'icon'   => 'plus', 
                'label'  => __("Nouvelle fonctionnalité"),
                'status' => 'success'
                ]
        ];
    }
    
    // draft','under_study','accepted','in_progress','alfa','beta','published','refused','deleted'
    
    public function getStatuses()
    {
        return [
            'draft' => [
                'label'  => __("brouillon"),
                'status' => 'default'
                ],
            'under_study' => [
                'label'  => __("en cours d'étude"),
                'status' => 'primary'
                ],
            'accepted' => [
                'label'  => __("accepté"),
                'status' => 'info'
                ],
            'in_progress' => [
                'label'  => __("en travaux"),
                'status' => 'warning'
                ],
            'test' => [
                'label'  => __("en test"),
                'status' => 'warning'
                ],
            'beta' => [
                'label'  => __("version beta"),
                'status' => 'success'
                ],
            'published' => [
                'label'  => __("réalisé"),
                'status' => 'success'
                ],
            'closed' => [
                'label'  => __("fermé"),
                'status' => 'success'
                ],
            'refused' => [
                'label'  => __("annulé"),
                'status' => 'danger'
                ],
            'deleted' => [
                'label'  => __("supprimé"),
                'status' => 'danger'
                ],
        ];
    }
}
