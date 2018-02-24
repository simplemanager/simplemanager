<?php
namespace Sma\Log;

use Osf\Log\AdapterInterface;
use Sma\Log;

/**
 * Adaptateur pour la ligne de commande
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage log
 */
class CliAdapter implements AdapterInterface
{
    const LOG_FILE = '/var/log/cli.log';
    
    public function log(string $message, string $level = Log::LEVEL_INFO, string $category = null, $dump = null) 
    {
        if (!Log::isLevelEnabled($level, $category)) {
            return false;
        }
        
        if ($dump) {
            $file = tempnam(sys_get_temp_dir(), 'log');
            file_put_contents($file, print_r($dump, true));
            $message .= ', see ' . $file;
        }
        $msg = "[" . $category . ':' . $level . '] ' . date('d/m/Y H:i:s ') . $message . "\n";
        file_put_contents(APP_PATH . self::LOG_FILE, $msg, FILE_APPEND);
    }
}
