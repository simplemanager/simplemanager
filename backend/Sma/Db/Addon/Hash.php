<?php
namespace Sma\Db\Addon;

use Osf\Crypt\Crypt;
use Sma\Log;

/**
 * Gestion des hashs
 * 
 * Hash d'accès unique aux enregistrements
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait Hash
{
    /**
     * Génère des hashs pour les enregistrements qui n'en contiennent pas
     * @param int|null $idAccount
     * @return int
     */
    public function fixAllHashes(?int $idAccount = null): int
    {
        $params = ['hash' => null];
        if ($idAccount) {
            $params['id_account'] = $idAccount;
        }
        $rowsWithNoHash = $this->select($params);
        foreach ($rowsWithNoHash as $row) {
            $row->setHash($this->generateHash())->save();
        }
        $count = $rowsWithNoHash->count();
        if ($count) {
            Log::info(sprintf(__("%d %s hashes updated"), $count, $this->getTableName()), 'HASH');
        }
        return $count;
    }
    
    /**
     * Retourne un hash d'accès unique
     * @return string
     */
    public function generateHash(): string
    {
        do {
            $hash = Crypt::getRandomHash(true);
            $exists = (bool) $this->select(['hash' => $hash])->count();
        } while ($exists);
        return $hash;
    }
}
