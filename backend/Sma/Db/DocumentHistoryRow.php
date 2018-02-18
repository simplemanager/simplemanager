<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractDocumentHistoryRow;

/**
 * Row model for table document_history
 *
 * Use this class to complete AbstractDocumentHistoryRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class DocumentHistoryRow extends AbstractDocumentHistoryRow
{
    use Addon\DocumentHistoryActions;

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