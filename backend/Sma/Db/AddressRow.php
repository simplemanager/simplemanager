<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractAddressRow;

/**
 * Row model for table address
 *
 * Use this class to complete AbstractAddressRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class AddressRow extends AbstractAddressRow
{

    /**
     * Put filters, validators and data cleaners here
     */
    public function set($field, $value)
    {
        return parent::set($field, $value);
    }

    /**
     * Put filters here
     */
    public function get($field)
    {
        return parent::get($field);
    }
    
    /**
     * With an address or a city
     * @return bool
     */
    public function isEmpty()
    {
        return !($this->getAddress() || $this->getCity());
    }
}