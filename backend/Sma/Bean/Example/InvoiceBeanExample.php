<?php
namespace Sma\Bean\Example;

use Sma\Bean\InvoiceBean;
use Sma\Bean\ContactBean;
use Sma\Bean\Addon\Example;
use Sma\Session\Identity;

/**
 * Exemples de factures
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class InvoiceBeanExample extends InvoiceBean
{
    use Example;
    
    public function __construct(int $exampleNo = 1)
    {
        parent::__construct();
        $this->loadExample($exampleNo);
    }
    
    protected function loadExample1()
    {
        $myName = Identity::get('company', 'title') ?: Identity::getFullname();
        $provider = ContactBean::buildContactBeanFromCompanyId(Identity::getIdCompany());
        $contact = new ContactBeanExample(1);
        $this->setCode('F0001')
            ->setType(self::TYPE_INVOICE)
            ->setConfidential()
            ->setProvider($provider)
            ->setRecipient($contact)
            // ->setObject('Votre facture suite à notre entretien téléphonique')
            ->setVref('Cmd0045')
            ->setAttn($contact->getComputedFullname())
            ->setDateSending(date('d/m/Y'))
            ->setDateValidity(date('d/m/Y', time() + (60 * 24 * 3600)))
            ->setTaxFranchise(!Identity::hasTax())
            ->setMdBefore("Pour le règlement de votre facture, notre service administratif se tient à votre disposition de 8H00 à 17H00 du lundi au vendredi au +33 1 23 45 67 89.")
            ->setMdAfter("Règlement par virement ou Chèque bancaire à l'ordre de " . $myName . "\nMerci pour votre confiance et à bientôt !")
            ->addProduct(new ProductBeanExample(1))
            ->addProduct(new ProductBeanExample(2))
            ->addProduct(new ProductBeanExample(3))
            ->addProduct(new ProductBeanExample(4));
    }
    
    protected function loadExample2()
    {
        $myName = Identity::get('company', 'title') ?: Identity::getFullname();
        $provider = ContactBean::buildContactBeanFromCompanyId(Identity::getIdCompany());
        $contact = new ContactBeanExample(2);
        $this->setCode('C0001')
            ->setType(self::TYPE_ORDER)
            ->setConfidential()
            ->setProvider($provider)
            ->setRecipient($contact)
            ->setAttn($contact->getComputedFullname())
            ->setDateSending(date('d/m/Y'))
            ->setDateValidity(date('d/m/Y', time() + (60 * 24 * 3600)))
            ->setTaxFranchise(!Identity::hasTax())
            ->addProduct(new ProductBeanExample(1))
            ->addProduct(new ProductBeanExample(2))
            ->addProduct(new ProductBeanExample(3))
            ->addProduct(new ProductBeanExample(4));
    }
    
    protected function loadExample3()
    {
        $myName = Identity::get('company', 'title') ?: Identity::getFullname();
        $provider = ContactBean::buildContactBeanFromCompanyId(Identity::getIdCompany());
        $contact = new ContactBeanExample(2);
        $this->setCode('D0001')
            ->setType(self::TYPE_QUOTE)
            ->setConfidential()
            ->setProvider($provider)
            ->setRecipient($contact)
            ->setAttn($contact->getComputedFullname())
            ->setDateSending(date('d/m/Y'))
            ->setDateValidity(date('d/m/Y', time() + (60 * 24 * 3600)))
            ->setTaxFranchise(!Identity::hasTax())
            ->addProduct(new ProductBeanExample(1))
            ->addProduct(new ProductBeanExample(2))
            ->addProduct(new ProductBeanExample(3))
            ->addProduct(new ProductBeanExample(4));
    }
}