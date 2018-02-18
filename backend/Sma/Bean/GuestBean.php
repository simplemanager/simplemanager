<?php
namespace Sma\Bean;

use Osf\Bean\AbstractBean;

/**
 * Information sur un invité
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class GuestBean extends AbstractBean
{
    protected $invoicesPayedCount     = 0;
    protected $invoicesToPayCount     = 0;
    protected $invoicesPayedAmountHt  = 0;
    protected $invoicesToPayAmountHt  = 0;
    protected $invoicesPayedAmountTtc = 0;
    protected $invoicesToPayAmountTtc = 0;
    protected $creditsPayedCount      = 0;
    protected $creditsToPayCount      = 0;
    protected $creditsPayedAmountHt   = 0;
    protected $creditsToPayAmountHt   = 0;
    protected $creditsPayedAmountTtc  = 0;
    protected $creditsToPayAmountTtc  = 0;
    
    protected $ordersToSign = 0;
    protected $ordersSigned = 0;
    
    protected $quotesToConsult = 0;
    protected $quotesConsulted = 0;
    
    protected $letterToRead = 0;
    protected $letterRead   = 0;
    
    /**
     * @param int $invoicesToPayCount
     * @return $this
     */
    public function setInvoicesToPayCount(int $invoicesToPayCount)
    {
        $this->invoicesToPayCount = $invoicesToPayCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getInvoicesToPayCount(): int
    {
        return (int) $this->invoicesToPayCount;
    }
    
    /**
     * @param int $invoicesPayedCount
     * @return $this
     */
    public function setInvoicesPayedCount(int $invoicesPayedCount)
    {
        $this->invoicesPayedCount = $invoicesPayedCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getInvoicesPayedCount(): int
    {
        return (int) $this->invoicesPayedCount;
    }
    
    /**
     * @param float $invoicesToPayAmountHt
     * @return $this
     */
    public function setInvoicesToPayAmountHt(float $invoicesToPayAmountHt)
    {
        $this->invoicesToPayAmountHt = $invoicesToPayAmountHt;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getInvoicesToPayAmountHt()
    {
        return $this->invoicesToPayAmountHt;
    }
    
    /**
     * @param float $invoicesPayedAmountHt
     * @return $this
     */
    public function setInvoicesPayedAmountHt(float $invoicesPayedAmountHt)
    {
        $this->invoicesPayedAmountHt = $invoicesPayedAmountHt;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getInvoicesPayedAmountHt()
    {
        return $this->invoicesPayedAmountHt;
    }
    
    /**
     * @param float $invoicesToPayAmountTtc
     * @return $this
     */
    public function setInvoicesToPayAmountTtc(float $invoicesToPayAmountTtc)
    {
        $this->invoicesToPayAmountTtc = $invoicesToPayAmountTtc;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getInvoicesToPayAmountTtc()
    {
        return $this->invoicesToPayAmountTtc;
    }
    
    /**
     * @param float $invoicesPayedAmountTtc
     * @return $this
     */
    public function setInvoicesPayedAmountTtc(float $invoicesPayedAmountTtc)
    {
        $this->invoicesPayedAmountTtc = $invoicesPayedAmountTtc;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getInvoicesPayedAmountTtc()
    {
        return $this->invoicesPayedAmountTtc;
    }
    
    /**
     * @param int $creditsToPayCount
     * @return $this
     */
    public function setCreditsToPayCount(int $creditsToPayCount)
    {
        $this->creditsToPayCount = $creditsToPayCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreditsToPayCount(): int
    {
        return (int) $this->creditsToPayCount;
    }
    
    /**
     * @param int $creditsPayedCount
     * @return $this
     */
    public function setCreditsPayedCount(int $creditsPayedCount)
    {
        $this->creditsPayedCount = $creditsPayedCount;
        return $this;
    }

    /**
     * @return int
     */
    public function getCreditsPayedCount(): int
    {
        return (int) $this->creditsPayedCount;
    }
    
    /**
     * @param float $creditsToPayAmountHt
     * @return $this
     */
    public function setCreditsToPayAmountHt(float $creditsToPayAmountHt)
    {
        $this->creditsToPayAmountHt = $creditsToPayAmountHt;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getCreditsToPayAmountHt()
    {
        return $this->creditsToPayAmountHt;
    }
    
    /**
     * @param float $creditsPayedAmountHt
     * @return $this
     */
    public function setCreditsPayedAmountHt(float $creditsPayedAmountHt)
    {
        $this->creditsPayedAmountHt = $creditsPayedAmountHt;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getCreditsPayedAmountHt()
    {
        return $this->creditsPayedAmountHt;
    }
    
    /**
     * @param float $creditsToPayAmountTtc
     * @return $this
     */
    public function setCreditsToPayAmountTtc(float $creditsToPayAmountTtc)
    {
        $this->creditsToPayAmountTtc = $creditsToPayAmountTtc;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getCreditsToPayAmountTtc()
    {
        return $this->creditsToPayAmountTtc;
    }
    
    /**
     * @param float $creditsPayedAmountTtc
     * @return $this
     */
    public function setCreditsPayedAmountTtc(float $creditsPayedAmountTtc)
    {
        $this->creditsPayedAmountTtc = $creditsPayedAmountTtc;
        return $this;
    }
    
    /**
     * @return float|null
     */
    public function getCreditsPayedAmountTtc()
    {
        return $this->creditsPayedAmountTtc;
    }
    
    /**
     * @param int $ordersToSign
     * @return $this
     */
    public function setOrdersToSign(int $ordersToSign)
    {
        $this->ordersToSign = $ordersToSign;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrdersToSign(): int
    {
        return (int) $this->ordersToSign;
    }
    
    /**
     * @param int $ordersSigned
     * @return $this
     */
    public function setOrdersSigned(int $ordersSigned)
    {
        $this->ordersSigned = $ordersSigned;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrdersSigned(): int
    {
        return (int) $this->ordersSigned;
    }
    
    /**
     * @param int $quotesToConsult
     * @return $this
     */
    public function setQuotesToConsult(int $quotesToConsult)
    {
        $this->quotesToConsult = $quotesToConsult;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuotesToConsult(): int
    {
        return (int) $this->quotesToConsult;
    }
    
    /**
     * @param int $quotesConsulted
     * @return $this
     */
    public function setQuotesConsulted(int $quotesConsulted)
    {
        $this->quotesConsulted = $quotesConsulted;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuotesConsulted(): int
    {
        return (int) $this->quotesConsulted;
    }
    
    /**
     * @param int $letterToRead
     * @return $this
     */
    public function setLetterToRead(int $letterToRead)
    {
        $this->letterToRead = $letterToRead;
        return $this;
    }

    /**
     * @return int
     */
    public function getLetterToRead(): int
    {
        return (int) $this->letterToRead;
    }
    
    /**
     * @param int $letterRead
     * @return $this
     */
    public function setLetterRead(int $letterRead)
    {
        $this->letterRead = $letterRead;
        return $this;
    }

    /**
     * @return int
     */
    public function getLetterRead(): int
    {
        return (int) $this->letterRead;
    }
}