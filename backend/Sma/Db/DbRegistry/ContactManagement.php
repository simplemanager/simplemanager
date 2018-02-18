<?php
namespace Sma\Db\DbRegistry;

use Zend\Db\Sql\Select;
use Sma\Session\Identity;
use Sma\Log;
use Exception;
use DB;

/**
 * Requêtes liées aux contacts
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait ContactManagement
{
    
    /**
     * Delete a contact
     * @param int $idContact
     * @param bool $deleteAddress
     * @param bool $checkIdAccount
     * @param bool $inTransaction
     * @return boolean
     * @throws Exception
     */
    public static function deleteContact(
            int $idContact, 
            bool $deleteAddress  = false,
            bool $checkIdAccount = true, 
            bool $inTransaction  = false)
    {
        try {

            $inTransaction || DB::getContactTable()->beginTransaction();
            
            // Get informations
            $contact = DB::getContactTable()->find($idContact);
            if (!$contact) {
                throw new Exception('Contact [' . $idContact . '] not found');
            }
            $checkIdAccount && self::checkIdAccount($contact->getIdAccount(), 'try to delete contact [' . $idContact . ']');
            
            // Delete address
            if ($deleteAddress && $contact->getIdAddress()) {
                self::deleteAddress($contact->getIdAddress(), $checkIdAccount, true);
            }
            
            // Delete contact
            $contact->delete();

            $inTransaction || DB::getContactTable()->commit();
            return true;
            
        } catch (Exception $e) {
            $inTransaction || DB::getContactTable()->rollback();
            Log::error('Contact delete error: ' . $e->getMessage(), 'DB', $e);
            if ($inTransaction) { throw $e; }
        }
        return false;
    }
    
    /**
     * Get list options with contacts
     * @param string $label
     * @return array
     */
    public static function getContactOptions(string $label = null)
    {
        $idAccount = Identity::getIdAccount();
        $select = new Select(DB::getCompanyTable()->getTableName());
        $select->columns(['id', 'title'])
               ->where(['company.id_account' => $idAccount, 'company.type' => 'client'])
               ->join('contact', 'contact.id=company.id_contact', ['firstname', 'lastname'], Select::JOIN_LEFT)
               ->order('title ASC');
        $result = DB::getCompanyTable()->selectWith($select);
        $options = $label === null ? [] : ['' => $label];
        foreach ($result as $row) {
            $value  = $row['title'];
            $value .= $row['title'] && ($row['firstname'] || $row['lastname']) ? ' (' : '';
            $value .= $row['firstname'] || $row['lastname'] ? trim($row['lastname'] . ' ' . $row['firstname']) : '' ;
            $value .= $row['title'] && ($row['firstname'] || $row['lastname']) ? ')' : '';
            $options[$row['id']] = $value;
        }
        return $options;
    }
    
    /**
     * L'utilisateur courant a-t-il au moins un contact ?
     * @return bool
     */
    public static function hasContact()
    {
        return (bool) DB::getContactTable()->select(['id_account' => Identity::getIdAccount(), 'is_account IS NULL'])->current();
    }
}