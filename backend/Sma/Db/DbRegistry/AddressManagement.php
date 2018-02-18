<?php
namespace Sma\Db\DbRegistry;

use Sma\Log;
use Exception;
use DB;

/**
 * Requêtes liées aux adresses
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait AddressManagement
{
    
    /**
     * Delete an address
     * @param int $idAddress
     * @param bool $checkIdAccount
     * @param bool $inTransaction
     * @return boolean
     * @throws Exception
     */
    public static function deleteAddress(
            int $idAddress, 
            bool $checkIdAccount = true, 
            bool $inTransaction  = false)
    {
        try {

            $inTransaction || DB::getAddressTable()->beginTransaction();
            
            // Get informations
            $address = DB::getAddressTable()->find($idAddress);
            if (!$address) {
                throw new Exception('Address [' . $idAddress . '] not found');
            }
            $checkIdAccount && self::checkIdAccount($address->getIdAccount(), 'try to delete address [' . $idAddress . ']');
            
            // Delete
            $address->delete();

            $inTransaction || DB::getAddressTable()->commit();
            return true;
            
        } catch (Exception $e) {
            $inTransaction || DB::getAddressTable()->rollback();
            Log::error('Company delete error: ' . $e->getMessage(), 'DB', $e);
            if ($inTransaction) { throw $e; }
        }
        return false;
    }
}