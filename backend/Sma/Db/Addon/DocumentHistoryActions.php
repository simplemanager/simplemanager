<?php
namespace Sma\Db\Addon;

use Sma\Bean\DocumentBeanInterface;
use Sma\Bean\InvoiceBean;
use H;

/**
 * Actions liées aux historiques de documents
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage db
 */
trait DocumentHistoryActions
{
    /**
     * @return DocumentBeanInterface|null
     */
    public function getBean(): ?DocumentBeanInterface
    {
        $bean = $this->getSource() ? unserialize($this->getSource()) : null;
        if ($bean instanceof InvoiceBean) {
            $bean->setIdDocumentHistory($this->getId());
        }
        return $bean;
    }
    
    /**
     * Construit l'url d'accès au document avec hash
     * @param string|null $recipientHash
     * @return string
     */
    public function buildUrlWithHash(?string $recipientHash = null): string
    {
        $params = ['k' => $this->getHash()];
        if ($recipientHash !== null) {
            $params['r'] = $recipientHash;
        }
        return (string) H::baseUrl(H::url('document', 'dl', $params), true);
    }
}
