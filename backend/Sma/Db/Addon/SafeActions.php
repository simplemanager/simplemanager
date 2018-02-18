<?php
namespace Sma\Db\Addon;

use Osf\Exception\ArchException;
use Sma\Session\Identity;
use Sma\Container as C;
use Sma\Log;
use ACL;

/**
 * Suppression sécurisée d'un enregistrement
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait SafeActions
{
    /**
     * Supprimer avec vérifications et nettoyages
     * @param int $id
     * @return int
     */
    public function deleteSafe($id)
    {
        $id = self::cleanId($id);
        
        // Suppression du document
        $idAccount = Identity::getIdAccount();
        $result = $this->delete(['id' => $id, 'id_account' => $idAccount]);
        
        // Nettoyage des données d'indexation
        if ($result) {
            if (defined('self::SEARCH_CATEGORY')) {
                C::getSearch()->cleanAutocomplete(self::SEARCH_CATEGORY, $id);
            }
        }
        
        // L'utilisateur courant n'a pas le droit de supprimer...
        else {
            Log::hack('Account [' . $idAccount . '] tried to delete [' . get_class($this) . '::' . $id . '].');
        }
        
        return $result;
    }
    
    /**
     * Cherche un enregistrement avec contôle d'identité
     * @param string $id
     * @return \Osf\Db\Row\AbstractRowGateway
     */
    public function findSafe($id)
    {
        // Nettoyages
        $id = self::cleanId($id);
        
        // Recherche de l'enregistrement
        return $this->select(['id_account' => Identity::getIdAccount(), 'id' => $id])->current();
    }
    
    /**
     * Doit être null ou numérique, nettoie en int ou null
     * @param mixed $id
     * @return int|null
     * @throws ArchException
     */
    public static function cleanId($id)
    {
        if (!(is_null($id) || is_numeric($id))) {
            throw new ArchException('Not a numeric or null id [' . $id . ']');
        }
        return $id === null ? null : (int) $id;
    }
    
    /**
     * Détermine et vérifie l'id account à utiliser
     * @param int|null $idAccount
     * @param bool $checkIsMyIdIfNotAdmin
     * @return int
     * @throws ArchException
     */
    public static function fixIdAccount(?int $idAccount = null, bool $checkIsMyIdIfNotAdmin = true): int
    {
        if ($checkIsMyIdIfNotAdmin 
         && $idAccount !== null
         && $idAccount !== Identity::getIdAccount()
         && !ACL::isAdmin()) {
            throw new ArchException('Id account is not current user one');
        }
        return $idAccount ?? Identity::getIdAccount();
    }
}
