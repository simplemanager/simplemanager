<?php
namespace App\Document\Model\Pdf;

use Osf\Exception\ArchException;
use Osf\Pdf\Document\Bean\AddressBean;
use Sma\Db\AddressRow;

/**
 * Hydrateur de lettre pdf à partir des données de la base
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 12 nov. 2013
 * @package company
 * @subpackage pdf
 */
class HelperAddressDbHydrator
{
    protected $title = '';
    
    /**
     * @var AddressRow
     */
    protected $address;
    
    /**
     * @param AddressBean $bean
     * @return AddressBean
     */
    public function hydrate(AddressBean $bean)
    {
        // Vérifications
        if (!$this->address) {
            throw new ArchException('No address specified');
        }
        
        // Hydratation
        if ($this->title) {
            $bean->setTitle($this->title);
        }
        $bean->populate($this->address->toArray(array('address', 'postal_code', 'city', 'country')));
        
        return $bean;
    }

    public function getTitle() {
        return $this->title;
    }
    
    /**
     * @param unknown $title
     * @return \App\Document\Model\Pdf\HelperAddressDbHydrator
     */
    public function setTitle($title) {
        $this->title = $title;
        return $this;
    }
    
    /**
     * @return \Www\Model\Db\AddressRow
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * @param AddressRow $address
     * @return \App\Document\Model\Pdf\HelperAddressDbHydrator
     */
    public function setAddress(AddressRow $address) {
        $this->address = $address;
        return $this;
    }
    
}