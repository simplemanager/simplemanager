<?php
namespace App\Invoice\Form;

use Osf\Form\OsfForm as Form;
use Osf\Filter\Filter as F;
use Osf\Validator\Validator as V;
use Osf\Form\Element\ElementTextarea;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Exception\ArchException;
use Osf\Stream\Text as T;
use Sma\Session\Identity as I;
use Sma\Bean\InvoiceBean;
use H;

/**
 * Send an invoice by email
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 * @deprecated
 */
class FormSend extends Form
{
    /**
     * @var InvoiceBean
     */
    protected $bean;
    
    public function __construct(InvoiceBean $bean)
    {
        $this->bean = $bean;
        parent::__construct();
    }
    
    protected function expert()
    {
        return I::isLevelExpert();
    }
    
    public function init()
    {
        $this->setTitle(__("Envoi par e-mail"), 'send');
        if (!$this->expert()) {
            $htmlBefore = H::msg(sprintf(__("Nous allons envoyer votre %s %s à %s. Vous pouvez éventuellement modifier les paramètres d'envoi. Pour une lecture sécurisée du document, un lien vers celui-ci sera proposé à votre client."), $this->bean->getTypeName(), $this->bean->getCode(), $this->bean->getRecipient()->getComputedTitle() . ' (' . $this->bean->getRecipient()->getEmail() . ')'));
            $this->setHtmlBefore($htmlBefore);
        }
        
        $subject = sprintf(__("%s | %s %s"), 
            $this->bean->getProvider()->getComputedTitle(),
            $this->bean->getTypeName(true),
            $this->bean->getCode()
        );
        
        $this->add((new ElementInput('subject'))
                ->setRequired()
                ->add(F::getStringTrim())
                ->add(V::getStringLength(3, 80))
                ->setLabel(__("Sujet"))
                ->setValue($subject));
        
        $body = $this->buildBody();
        
        $this->add((new ElementTextarea('body'))
                ->setRequired()
                ->add(F::getStringTrim())
                ->setLabel(__("Contenu du message"))
                ->setValue($body));
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Envoyer")));
    }
    
    protected function buildBody()
    {
        $body = sprintf(__("%s,\n\nVeuillez trouver ci-joint %s %s pour un montant de %s TTC."),
            $this->bean->getRecipient()->getComputedCivilityWithLastname(), 
            $this->bean->getTypeName(false, InvoiceBean::getPrefixesSingular()),
            $this->bean->getCode(),
            T::currencyFormat($this->bean->getTotalTtcWithDiscount())
        ) . ' ';
        $isInvoice = $this->bean->isInvoice();
        $date = $this->bean->getDateValidity() ?? ($isInvoice ? (new \DateTime())->setTimestamp(time() + (3600 * 24 * 30)) : null);
        if ($date !== null) {
            $dateValidity = T::formatDate($date);
            switch ($this->bean->getType()) {
                case InvoiceBean::TYPE_QUOTE : 
                    $body .= sprintf(__("Vous avez jusqu'au %s pour répondre à cette offre."), $dateValidity);
                    break;
                case InvoiceBean::TYPE_ORDER : 
                    $body .= sprintf(__("Votre commande est valable jusqu'au %s."), $dateValidity);
                    break;
                case InvoiceBean::TYPE_INVOICE : 
                    $body .= sprintf(__("La date limite de paiement de votre facture est fixée au %s."), $dateValidity);
                    break;
                default : 
                    throw new ArchException('bad bean type ' . $this->bean->getType());
            }
        }
        
        $signature = $this->bean->getProvider()->getComputedFullname(false) ?: $this->bean->getProvider()->getComputedTitle();
        $body .= "\n\n" . __("Nous restons à votre disposition pour toute information complémentaire.");
        $body .= "\n\n" . __("Bien cordialement") . ",  \n" . $signature;
        
        return $body;
    }
}
