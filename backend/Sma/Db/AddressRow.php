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
     * With an address or a city
     * @return bool
     */
    public function isEmpty()
    {
        return !($this->getAddress() || $this->getCity());
    }
}