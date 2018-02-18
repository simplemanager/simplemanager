<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractDocumentHistoryCurrentRow;

/**
 * Row model for table document_history_current
 *
 * Use this class to complete AbstractDocumentHistoryCurrentRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class DocumentHistoryCurrentRow extends AbstractDocumentHistoryCurrentRow
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
    
    public function getId()
    {
        return parent::getIdDocumentHistory();
    }
}