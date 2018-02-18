<?php
namespace App\Recipient\Form;

use App\Guest\Controller as GuestController;
use Sma\Session\Identity as I;
use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementTags;
use Osf\Form\Element\ElementCheckbox;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Filter\Filter as F;
use Osf\Validator\Validator as V;
use Sma\Form\Addon\ForModal;
use Sma\Bean\ContactBean;
use DB;

/**
 * Login / Pass
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormRecipient extends Form
{
    use ForModal;
    
    public function init()
    {
        // Titre si on est pas dans un modal
        $this->isInModal() || $this->setTitle(__("Modifier un contact"));
        
        $companyForm = DB::getCompanyTable()->getForm()->setOptional()->displayLabels(false);
        $contactForm = DB::getContactTable()->getForm()->setOptional()->displayLabels(false);
        
        // Champs du formulaire principal
        $this->add($contactForm->getElement('civility')->setPrefix('u')->setPlaceholder(null))
             ->add($contactForm->getElement('firstname')->setPrefix('u')->setPlaceholder(__("Prénom")))
             ->add($contactForm->getElement('lastname')->setPrefix('u')->setPlaceholder(__("Nom")))
             ->add($companyForm->getElement('title')->setPrefix('c')->setPlaceholder(__("Société")))
             ->add($companyForm->getElement('email')->setPrefix('c')->setPlaceholder(__("E-mail")))
             ->add($contactForm->getElement('tel')->setPrefix('c')->setRelevanceLow())
             ->add($contactForm->getElement('gsm')->setPrefix('u')->setRelevanceLow())
             ->add($contactForm->getElement('fax')->setPrefix('c')->setRelevanceLow())
             ->add($companyForm->getElement('url')->setPrefix('c')->setPlaceholder(__("Site web (url)"))->setRelevanceLow())
             ->add((new ElementTags('description'))
                ->setPrefix('c')
                ->setRelevanceLow()
                ->setPlaceholder(__("Mots clés, ex: commerce, particulier, vip"))
                ->setTooltip(__("Pour améliorer vos recherches"))
                ->setFilters($companyForm->getElement('description')->getFilters())
                ->setValidators($companyForm->getElement('description')->getValidators()));

        // Facturation HT
        $desc = GuestController::isLogged() 
                ? __("Cochez cette case si vous devez être facturé sans la TVA. En cas de doute ne modifiez pas  cette option.")
                : (I::isLogged() && !I::isLevelExpert() 
                    ? (I::hasTax() ? '' : __("Cette option n'a pas d'effet avec la franchise ne base de TVA.") . ' ') . __("Cochez cette case pour générer des factures hors taxe par défaut pour ce client. Cette option devrait être cochée pour les facturations hors UE. Pour les facturations dans l'UE et en dehors de la France, la facturation HT est appliquée dans certains cas tels que la vente de biens à un client assujetti à la TVA dans l'UE (autoliquidation), ou non assujetti à la TVA.")
                    : null);
        $label = GuestController::isLogged() 
                ? __("Recevoir des factures hors taxe par défaut (sans TVA)")
                : __("Facturer hors taxe par défaut (sans la TVA)");
        $this->add((new ElementCheckbox('ht'))
                ->setLabel($label)
                ->setPrefix('b')
                ->setDescription($desc)
                ->setRelevanceLow());
        
        // TVA Intracommunautaire
        $this->add($companyForm->getElement('tva_intra')->setPrefix('c')->setRelevanceLow());
        
        // Adresse de facturation 
        $addressForm = DB::getAddressTable()->getForm()
                ->setOptional()
                ->displayLabels(false)
                ->setCollapsable()
                ->setTitle(__("Adresse de facturation"), 'home');
        $addressForm->getElement('address')->setPlaceholder(__("Rue (siège social ou domicile)"));
        $this->addSubForm('a', $addressForm);
        
        // Adresse de livraison
        $deliAddForm = DB::getAddressTable()->getForm()->setOptional()->displayLabels(false)->setTitle(__("Adresse de livraison"), 'truck');
        $deliAddForm->getElement('address')->setPlaceholder(__("Rue (laisser vide si identique l'adresse de facturation)"));
        $this->addSubForm('d', $deliAddForm);
        
        // Bouton caché pour l'activation du post avec la touche "enter"
        if ($this->isInModal()) {
            $this->add((new ElementSubmit('submit'))->getHelper()->addCssClass('hidden')->getElement());
        } else {
            $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
        }
    }
    
    /**
     * Ajoute les valeurs du bean au formulaire
     * @param ContactBean $bean
     * @return void
     */
    public function loadBean(ContactBean $bean): void
    {
    }
    
    public function isValid($values = null)
    {
        $valid = parent::isValid($values);
        $vals = $this->getValues();
        
        // Si on facture HT, il faut la TVA Intra
        if ($vals['b']['ht'] && !$vals['c']['tva_intra']) {
            $msg = GuestController::isLogged() 
                    ? __("Votre TVA Intracommunautaire est obligatoire si vous voulez être facturé hors taxe.")
                    : __("La TVA Intracommunautaire de votre client est obligatoire pour lui envoyer des factures hors taxe.");
            $this->getElement('tva_intra')->addError($msg);
            $valid = false;
        }
        
        // Au moins un titre ou un prénom ou un nom
        if (!$vals['c']['title'] && !$vals['u']['firstname'] && !$vals['u']['lastname']) {
            $this->getElement('title')->addError(__("...et/ou un nom de société"));
            $this->getElement('firstname')->addError(__("Entrez un prénom..."));
            $this->getElement('lastname')->addError(__("...et/ou un nom"));
            $valid = false;
        }
        
        return $valid;
    }
}