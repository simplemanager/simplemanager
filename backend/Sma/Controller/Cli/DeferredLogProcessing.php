<?php
namespace Sma\Controller\Cli;

use Osf\Controller\Cli\AbstractDeferredAction;
use DB, C;

/**
 * Gestion des logs en différé
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage controller
 */
class DeferredLogProcessing extends AbstractDeferredAction
{
    const REDIS_LKEY = 'LOG';
    
    public function getName(): string
    {
        return "Logs registration and processing [" . C::getRedis()->lLen(self::REDIS_LKEY) . '] item(s)';
    }

    public function execute()
    {
        $return = null;
        while($row = C::getRedis()->lPop(self::REDIS_LKEY)) {
            try {
                DB::getLogTable()->insert(unserialize($row));
                $return = $return === null ? true : $return && true;
            } catch (\Exception $e) {
                $this->registerException($e, 'log', $row);
                $return = false;
            }
        }
        return $return;
    }
}