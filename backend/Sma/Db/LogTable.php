<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractLogTable;
use Osf\Helper\Mysql;

/**
 * Table model for table log
 *
 * Use this class to complete AbstractLogTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class LogTable extends AbstractLogTable
{
    public function getLogForTable(array $settings = [])
    {
        if (isset($settings['trace']) && is_int($settings['trace']) && $settings['trace']) {
            $traceRow = $this->find($settings['trace']);
            $settings = [
                'account' => $traceRow->getIdAccount(),
                'until' => $traceRow->getDateInsert()
            ];
        }
        
        $params = [];
        $sql = 'SELECT id, level, message, page, ip, date_insert, category, id_account '
                . 'FROM log ' // , sma_admin.account '
                . 'WHERE 1 '; // log.id_account=sma_admin.account.id ';
        if (isset($settings['category']) && $settings['category']) {
            $sql .= 'AND category = ? ';
            $params[] = $settings['category'];
        }
        if (isset($settings['account']) && $settings['account']) {
            $sql .= 'AND id_account = ? ';
            $params[] = $settings['account'];
        }
        if (isset($settings['until']) && $settings['until']) {
            $sql .= 'AND log.date_insert <= ? ';
            $params[] = $settings['until'];
        }
        if (isset($settings['level']) && $settings['level'] && count($settings['level']) !== 3) {
            $sql .= 'AND (0';
            foreach ($settings['level'] as $level) {
                $sql .= ' OR level = ?';
                $params[] = $level;
            }
            $sql .= ') ';
        }
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND log.date_insert >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND log.date_insert <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND (log.message LIKE ? OR log.page LIKE ?) ';
            $like = Mysql::like($settings['q']);
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= 'ORDER BY log.date_insert DESC';
        return $this->prepare($sql)->execute($params);
    }
}