<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractAddressContactTable;
use Sma\Session\Identity;
use Osf\Helper\Tab;
use DB;

/**
 * Table model for table address_contact
 *
 * Use this class to complete AbstractAddressContactTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class AddressContactTable extends AbstractAddressContactTable
{
    /**
     * @param array $values
     * @return boolean
     */
    public function updateCurrentUser(array $values, bool $privateData = false)
    {
        $idAccount = Identity::get('id');
        $contact = DB::getContactTable()->select('is_account=' . (int) $idAccount)->toArray()[0];
        
        $dataAddress = $privateData ? $values['a'] : null;
        $dataContact = $values['c'];
        $dataAccount = Tab::reduce($values['c'], ['firstname', 'lastname']);
        
        $this->beginTransaction();
        try {

            // Address
            if ($privateData) {
                if ($contact['id_address']) {
                    DB::getAddressTable()->update($dataAddress, 'id=' . (int) $contact['id_address']);
                } else {
                    DB::getAddressTable()->insert($dataAddress);
                    $dataContact['id_address'] = DB::getAddressTable()->lastInsertValue;
                }
            }

            // Contact
            DB::getContactTable()->update($dataContact, 'is_account=' . (int) $idAccount);

            // Account
            DB::getAccountTable()->update($dataAccount, 'id=' . (int) $idAccount);
            
            $this->commit();
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }
        return true;
    }
}