<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractTicketWithPollsTable;
use Sma\Session\Identity;
use Osf\Helper\Mysql;
use App\Ticket\Form\FormFilter;
use ACL;

/**
 * Table model for table ticket_with_polls
 *
 * Use this class to complete AbstractTicketWithPollsTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class TicketWithPollsTable extends AbstractTicketWithPollsTable
{
    public function getTicketsForTable(array $settings = [])
    {
        $sorts = [
            'd'   => 'ticket_with_polls.category, ticket_with_polls.poll_count DESC',
            'ca'  => 'ticket_with_polls.category ASC, ticket_with_polls.poll_count DESC',
            'cd'  => 'ticket_with_polls.category DESC, ticket_with_polls.poll_count DESC',
            'pa'  => 'ticket_with_polls.status ASC, ticket_with_polls.poll_count DESC',
            'pd'  => 'ticket_with_polls.status DESC, ticket_with_polls.poll_count DESC',
            'va'  => 'ticket_with_polls.poll_count ASC',
            'vd'  => 'ticket_with_polls.poll_count DESC',
            'dca' => 'ticket_with_polls.date_insert ASC',
            'dcd' => 'ticket_with_polls.date_insert DESC',
            'dua' => 'ticket_with_polls.date_update ASC',
            'dud' => 'ticket_with_polls.date_update DESC'
        ];
        
        $types = array_filter(array_keys(FormFilter::getTypeOptions()));
        $progresses = array_filter(array_keys(FormFilter::getProgressionOptions()));
        $sort = isset($settings['s']) && isset($sorts[$settings['s']]) ? $sorts[$settings['s']] : $sorts['dud'];
        
        $params = ACL::isAdmin() ? [Identity::getIdAccount()] : [Identity::getIdAccount(), Identity::getIdAccount()];
        $sql = 'SELECT ticket_with_polls.*, count(ticket_poll.id_account) as voted '
                . 'FROM ticket_with_polls '
                . 'LEFT JOIN ticket_poll ON ticket_poll.id_ticket=ticket_with_polls.id AND ticket_poll.id_account=? '
                . 'WHERE 1 '
                . (!ACL::isAdmin() || !isset($settings['pr']) || $settings['pr'] !== 'deleted' ? 'AND status <> \'deleted\' ' : '')
                . (!isset($settings['pr']) || $settings['pr'] !== 'closed' ? 'AND status <> \'closed\' ' : '')
                . (!isset($settings['pr']) || $settings['pr'] !== 'refused' ? 'AND status <> \'refused\' ' : '')
                . (ACL::isAdmin() ? '' : 'AND (status <> \'draft\' OR ticket_with_polls.id_account=?) ')
                . (ACL::isAdmin() ? '' : 'AND visibility = \'public\' ')
                . (ACL::isAdmin() ? '' : 'AND status <> \'closed\' ');
        if (isset($settings['ty']) && in_array($settings['ty'], $types)) {
            $sql .= 'AND ticket_with_polls.category = ? ';
            $params[] = $settings['ty'];
        }
        if (isset($settings['pr']) && in_array($settings['pr'], $progresses)) {
            $sql .= 'AND ticket_with_polls.status = ? ';
            $params[] = $settings['pr'];
        }
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND ticket_with_polls.date_update >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND ticket_with_polls.date_update <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND (ticket_with_polls.title LIKE ? OR ticket_with_polls.content LIKE ?) ';
            $like = Mysql::like($settings['q']);
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= 'GROUP BY ticket_with_polls.id '
             .  'ORDER BY ' . $sort;
        return $this->prepare($sql)->execute($params);
    }
}

