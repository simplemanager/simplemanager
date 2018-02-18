<?php
namespace Sma\Controller\Cli;

use Osf\Controller\Cli\AbstractDeferredAction;
use Osf\Crypt\Crypt;
use Osf\Exception\ArchException;
use Sma\Mail;
use C;

/**
 * Gestion des mails à envoyer en différé
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage controller
 */
class DeferredMailProcessing extends AbstractDeferredAction
{
    const REDIS_HKEY = 'DMAIL';
    
    public function getName(): string
    {
        return "Mail processing, [" . C::getRedis()->hLen(self::REDIS_HKEY) . '] mail(s) to send';
    }

    public function execute()
    {
        $mails = C::getRedis()->hKeys(self::REDIS_HKEY);
        $return = null;
        foreach ($mails as $mailKey) {
            $row = C::getRedis()->hGet(self::REDIS_HKEY, $mailKey);
            try {
                $mail = unserialize($row);
                if (!($mail instanceof Mail)) {
                    throw new ArchException('Entry is not an Sma\Mail');
                }
                $return !== null && usleep(500);
                $mail->send();
                C::getRedis()->hDel(self::REDIS_HKEY, $mailKey);
                $return = $return === null ? true : $return && true;
            } catch (\Exception $e) {
                $this->registerException($e, 'mail', $row);
                $return = false;
            }
        }
        return $return;
    }
    
    /**
     * Ajoute un e-mail à envoyer à la liste d'attente. 
     * @param Mail $mail
     * @param string $mailKey
     * @return int|false
     */
    public static function registerMail(
            Mail $mail, 
            $mailKey = null)
    {
        $mailKey = $mailKey === null ? Crypt::getRandomHash() : (string) $mailKey;
        return C::getRedis()->hSet(self::REDIS_HKEY, $mailKey, serialize($mail));
    }
}
