<?php
namespace Sma\Log;

use Osf\Log\AdapterInterface;
use Osf\Stream\Text as T;
use Sma\Session\Identity;
use Sma\Log;
use C;

/**
 * Envoi des logs dans le cache Redis pour enregistrement différé
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage log
 */
class DbAdapter implements AdapterInterface
{
    /**
     * @param string $message
     * @param string $level
     * @param string $category
     * @param mixed $dump
     */
    public function log(string $message, string $level = Log::LEVEL_INFO, string $category = null, $dump = null)
    {
        if (!Log::isLevelEnabled($level, $category)) {
            return false;
        }
        
        $pageInfo = [
            'get'     => $_GET,
            'post'    => $_POST,
            'cookie'  => $_COOKIE,
            'server'  => $_SERVER,
        //    'session' => $_SESSION,
            'files'   => $_FILES
        ];
        
        if (isset($pageInfo['post']['password'])) {
            $pageInfo['post']['password'] = str_repeat('*', strlen($pageInfo['post']['password']));
        }
        
        $pageInfo = var_export($pageInfo, true);
        
        // @task Trouver une solution plus propre pour isLogged() qui fait une ErrorException
        // dans un contexte CLI ou sans identité.
        $row = [
            'message'     => $message,
            'level'       => $level,
            'category'    => $category === null || !$category ? null : $category,
            'ip'          => (string) filter_input(INPUT_SERVER, 'REMOTE_ADDR'),
            'id_account'  => @Identity::isLogged() ? (int) Identity::getIdAccount() : null,
            'page'        => (string) filter_input(INPUT_SERVER, 'REQUEST_URI'),
            'page_info'   => (string) $pageInfo,
            'date_insert' => date('Y-m-d H:i:s'),
            'dump'        => $dump !== null ? T::crop(print_r($dump, true), 60000) : null
        ];
        
        C::getRedis()->lPush('LOG', serialize($row));
        return true;
    }
}