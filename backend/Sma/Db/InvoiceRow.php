<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractInvoiceRow;
use Sma\Bean\InvoiceBean;

/**
 * Row model for table invoice
 *
 * Use this class to complete AbstractInvoiceRow
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class InvoiceRow extends AbstractInvoiceRow
{
    /**
     * Complète le bean avec des informations qui ont potentiellement changées
     * @return InvoiceBean
     */
    public function getBeanUpToDate()
    {
        $bean = $this->getBean();
        if ($bean instanceof InvoiceBean) {
            $bean->setIdInvoice($this->getId());
            $bean->setStatus($this->getRelatedDocumentRowFromIdDocumentFk()->getStatus());
        }
        return $bean;
    }
}