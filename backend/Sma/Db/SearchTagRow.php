<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractSearchTagRow;

/**
 * Row model for table search_tag
 *
 * Use this class to complete AbstractSearchTagRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class SearchTagRow extends AbstractSearchTagRow
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
}