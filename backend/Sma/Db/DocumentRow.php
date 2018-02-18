<?php
namespace Sma\Db;

use Osf\Pdf\Document\Bean\BaseDocumentBean;
use Sma\Db\Generated\AbstractDocumentRow;
use DB;

/**
 * Row model for table document
 *
 * Use this class to complete AbstractDocumentRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class DocumentRow extends AbstractDocumentRow
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
     * @return BaseDocumentBean
     */
    public function getBean(): BaseDocumentBean
    {
        return DB::getDocumentTable()->getBean($this->getId());
    }
}