<?php
namespace Sma\Safety;

use Osf\Safety\Firewall as OsfFirewall;
use Sma\Log;

/**
 * HTTP high level firewall
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage safety
 */
class Firewall extends OsfFirewall
{
    /**
     * Tarpit le client courant
     * @param type $msgDump
     * @param bool $die
     * @param int|null $duration
     */
    public function tarpit($msgDump = null, bool $die = false, ?int $duration = null)
    {
        parent::tarpit($msgDump, false, $duration);
        Log::getAdapter(); // @task [TASK] Putain de pb d'adapter ici si on appel pas getAdapter() manuellement.
        Log::hack('Tarpit triggered from ' . $this->getKeyPrefix(), $msgDump); 
        $die && exit;
    }
}
