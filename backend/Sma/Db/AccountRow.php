<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractAccountRow;

/**
 * Row model for table account
 *
 * Use this class to complete AbstractAccountRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class AccountRow extends AbstractAccountRow
{
//    protected $extraData = [
//        'skin' => null
//    ];

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
}