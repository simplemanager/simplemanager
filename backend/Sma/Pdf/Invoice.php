<?php
namespace Sma\Pdf;

use Osf\Pdf\Tcpdf\Invoice as TcpdfInvoice;
use Sma\Bean\InvoiceBean;
use Sma\Pdf\DocumentInterface;
use Sma\Bean\DocumentBeanInterface;

/**
 * Osf Tcpdf Invoice avec des personnalisations spécifique à l'application
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage pdf
 */
class Invoice extends TcpdfInvoice implements DocumentInterface
{
    use Addon\SmaParameters;
    use Addon\Dump;
    
    public function __construct(InvoiceBean $bean) {
        parent::__construct($bean);
        $this->registerSmaParameters($this);
    }
    
    /**
     * Clé primaire
     */
    public function getId()
    {
        return $this->getBean()->getId();
    }
    
    /**
     * Titre du document
     */
    public function getTitle(): string
    {
        return $this->getBean()->getTitle();
    }
    
    /**
     * Type de document : letter, invoice, order, quote, form
     */
    public function getType(): string
    {
        return $this->getBean()->getType();
    }
    
    /**
     * Description avec mots clés à indexer
     */
    public function getDescription(): string
    {
        return ''; // TODO
    }
    
    /**
     * Mots clés à indexer pour le moteur de recherche
     */
    public function getSearchContent(): string
    {
        return $this->getTitle(); // TODO
    }
    
    /**
     * Bean contenant l'ensemble des données du document au moment de l'enregistrement
     * @return \Sma\Bean\InvoiceBean
     */
    public function getBean(): ?DocumentBeanInterface
    {
        return parent::getBean();
    }
    
    /**
     * Statut de l'invoice au moment de l'enregistrement du document
     * @return string|null
     */
    public function getStatus(): ?string
    {
        return $this->getBean()->getStatus();
    }
}
