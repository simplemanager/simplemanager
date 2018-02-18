<?php
namespace App\Invoice\Model\Bean;

use Osf\Pdf\Document\Bean\ProductBean;
use Osf\Helper\DateTime as DT;
use Osf\Stream\Text;
use Sma\Bean\InvoiceBean;
use Sma\Bean\ContactBean;
use Sma\Session\Identity;
use App\Invoice\Form\FormInvoice;
use DB;

/**
 * Hydrateur depuis les données de formulaire
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage invoice
 */
class InvoiceBeanHydrator
{
    protected $invoice;
    
    public function __construct(InvoiceBean $invoice = null)
    {
        $invoice !== null && $this->setInvoiceBean($invoice);
    }
    
    /**
     * @param InvoiceBean $invoice
     * @return $this
     */
    public function setInvoiceBean(InvoiceBean $invoice)
    {
        $this->invoice = $invoice;
        return $this;
    }
    
    public function getInvoiceBean(): InvoiceBean
    {
        if (!$this->invoice) {
            $this->invoice = new InvoiceBean();
        }
        return $this->invoice;
    }
    
    /**
     * Hydrate InvoiceBean from FormInvoice
     * @param FormInvoice $form
     * @return $this
     */
    public function hydrate(FormInvoice $form)
    {
        // Récupération des informations
        $invoice    = $this->getInvoiceBean();
        $products   = $form->getProducts();
        $dbProducts = $form->getDbProducts();
        $values     = $form->getValues();
        
        // Envoyeur
        $values['provider'] = ContactBean::buildContactBeanFromContactId(Identity::getIdContact(), true);
        
        // Destinataire
        $values['recipient'] = $values['recipient'] ? DB::getCompanyTable()->getContactBean($values['recipient']) : new ContactBean();
        
        // Enregistrement : envoyeur, destinataire, informations complémentaires
        $values['type'] = $form->getType();
        
        // Enregistrement des valeurs
        $invoice->populate($values);
        
        // Si le code est vide, mettre le numéro de preview automatique et 
        // définir le n° de code comme étant automatique
        if ($values['code'] === '') {
            $invoice->setCode($form->getPreviewCodeNumber(), true);
            $invoice->setCodeAuto();
        }
        
        // Date d'envoi / facturation vide = date du jour
        if (!$values['date_sending']) {
            $invoice->setDateSending(time()); // maintenant
        }
        
        
        // Date de validité vide
        if (!$values['date_validity']) {
            
            // Si c'est pas une facture, on va chercher le délai
            if (!$invoice->isInvoice()) {
                $delay = (int) Identity::getParam('invoice', 'delay_other');
                $validity = $invoice->getDateSending()->getTimestamp() + (3600 * 24 * (int) $delay);
                $invoice->setDateValidity($validity);
            }
            
            // Si c'est une facture, on calcule en fonction des préférences utilisateur
            else {
                switch (Identity::getParam('invoice', 'delay_type')) {

                    // Délai de validité basé sur du date à date
                    case InvoiceBean::VALIDITY_TYPE_DELAY : 
                        $delay = Identity::getParam('invoice', 'delay');
                        $delay = is_numeric($delay) ? (int) $delay : 60;
                        $validity = $invoice->getDateSending()->getTimestamp() + (3600 * 24 * (int) $delay);
                        $invoice->setDateValidity($validity);
                        break;

                    // 45 jours + fin de mois
                    case InvoiceBean::VALIDITY_TYPE_45FM : 
                        $validity = DT::getLastDayOf($invoice->getDateSending()->getTimestamp() + (3600 * 24 * 45));
                        $invoice->setDateValidity($validity);
                        break;

                    // Fin de mois + 45 jours
                    case InvoiceBean::VALIDITY_TYPE_FM45 : 
                        $validity = DT::getLastDayOf($invoice->getDateSending()->getTimestamp()) + (3600 * 24 * 45);
                        $invoice->setDateValidity($validity);
                        break;

                    // Paiement comptant le jour même
                    case InvoiceBean::VALIDITY_TYPE_CASH : 
                        $invoice->setDateValidity($invoice->getDateSending()->getTimestamp());
                        break;

                    // Paiement à la réception : 1 semaine 
                    case InvoiceBean::VALIDITY_TYPE_DELIVERY : 
                        $invoice->setDateValidity($invoice->getDateSending()->getTimestamp() + (3600 * 24 * 7));
                        break;

                    // Paiement périodique : délai de 45 jours
                    case InvoiceBean::VALIDITY_TYPE_PERIODIC : 
                        $invoice->setDateValidity($invoice->getDateSending()->getTimestamp() + (3600 * 24 * 45));
                        break;
                }
            }
        }
                
        // Pré-remplissage des produits et récupération des ids
        $totalPrice = 0;
        $totalProducts = 0;
//        $warnings = [];
        foreach ($products as $product) {
            
            // Récupération du produit dans la base, création du bean, populate
            $productId = (int) $product['pd'];
            $dbProduct = $dbProducts[$productId];
            $bean = new ProductBean();
            $bean->populate($dbProduct, true);
            
            // Surcharge par les données du formulaire. S'il n'y a pas de 
            // prix ou de discount, on prend celui par défaut, sinon on 
            // définit le prix comme étant fixé (on prendra le même tarif en 
            // cas de modification ou clonage de la facture)
            $bean->setQuantity($product['pq']);
            isset($product['pp'])  && $bean->setPrice($product['pp'])    && $bean->setPriceIsDefault(false);
            isset($product['pr'])  && $bean->setDiscount($product['pr']) && $bean->setDiscountIsDefault(false);
            isset($product['pdd']) && $bean->setComment($product['pdd']);
            $totalPrice += $bean->getTotalPriceHT();
            $totalProducts += 1;
            $invoice->addProduct($bean);
            
            // Detection de changement de prix ou discounts
//            if ($bean->getPriceIsDefault() && isset($product['pp']) && $bean->getPriceHT() !== $product['pp']) {
//                $warnings = sprintf(__("Produit %s : ancien prix = %s, nouveau prix = %s"), $bean->getTitle(), $product['pp'], $bean->getPriceHT());
//            }
        }
        
        // Affichage de modification de tarifs ou remises
//        if ($warnings) {
//            C::getJsonRequest()->addAlert(__("Des prix ou remises ont été modifiés"), implode(' ; ', $warnings), 'warning');
//        }
        
        // Paufinage du titre et de la description
        $ib = $this->getInvoiceBean();
        $type = Text::ucFirst($ib->getTypeName());
        $code = $ib->getCode();
        $providerName = $ib->getRecipient()->getComputedTitle();
        $ib->setTitle($code . ' - ' 
                . $providerName . ' - ' 
                . $totalProducts . ' ' . ($totalProducts > 1 ?__("produits") : __("produit")) . ' - '
                . Text::currencyFormat($totalPrice) . ' ' . __("HT"));
        $descString = __("%s %s à destination de %s, %d produit(s) différents pour un total de %s%s");
        $descValues = [$type, $code, $providerName, $totalProducts, Text::currencyFormat($totalPrice), Identity::hasTax() ? ' HT' : ''];
        $ib->setDescription(vsprintf($descString, $descValues));
        
        return $this;
    }
}
