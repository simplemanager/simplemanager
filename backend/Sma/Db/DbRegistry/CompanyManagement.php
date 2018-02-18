<?php
namespace Sma\Db\DbRegistry;

use Sma\Log;
use Exception;
use DB;

/**
 * Requêtes liées aux sociétés
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait CompanyManagement
{
    /**
     * Delete a company
     * @param int $idCompany
     * @param bool $deleteAddress
     * @param bool $deleteContacts
     * @param bool $checkIdAccount
     * @param bool $inTransaction
     * @return boolean
     * @throws Exception
     */
    public static function deleteCompany(
            int $idCompany,
            bool $deleteAddress  = false,
            bool $deleteContacts = false,
            bool $checkIdAccount = true, 
            bool $inTransaction  = false): void
    {
        try {

            $inTransaction || DB::getCompanyTable()->beginTransaction();
            
            // Get informations
            $company = DB::getCompanyTable()->find($idCompany);
            if (!$company) {
                throw new Exception('Company [' . $idCompany . '] not found');
            }
            $checkIdAccount && self::checkIdAccount($company->getIdAccount(), 'try to delete company [' . $idCompany . ']');
            
            // Delete address
            if ($deleteAddress && $company->getIdAddress()) {
                self::deleteAddress($company->getIdAddress(), $checkIdAccount, true);
            }
            
            // Delete main contact
            if ($deleteContacts && $company->getIdContact()) {
                self::deleteContact($company->getIdContact(), $deleteAddress, $checkIdAccount, true);
            }
            
            // Delete other contacts
//            if ($deleteContacts) {
//                $contacts = DB::getContactTable()->select(['id_company' => $idCompany]);
//                foreach ($contacts as $contact) {
//                    self::deleteContact($contact->getId(), $deleteAddress, $checkIdAccount, true);
//                }
//            }
            
            // Delete company
            $company->delete();

            // Validation
            $inTransaction || DB::getCompanyTable()->commit();
            
        } catch (Exception $e) {
            $inTransaction || DB::getCompanyTable()->rollback();
            Log::error('Company delete error: ' . $e->getMessage(), 'DB', $e);
            throw $e;
        }
    }
}