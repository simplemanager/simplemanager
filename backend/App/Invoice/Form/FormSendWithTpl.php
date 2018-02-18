<?php
namespace App\Invoice\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementSelect;
use Osf\Form\Element\ElementSubmit;
use Osf\Exception\ArchException;
use Sma\Session\Identity as I;
use Sma\Bean\InvoiceBean;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use H;

/**
 * Send an invoice by email with a template
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormSendWithTpl extends Form
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
        $this->setTitle(sprintf(__("Envoi de %s (%s)"), $this->bean->getCode(), $this->bean->getTypeName()), 'send');
        if (!$this->expert()) {
            $htmlBefore  = (string) H::msg(sprintf(__("Nous allons envoyer votre %s %s à %s."), $this->bean->getTypeName(), $this->bean->getCode(), $this->bean->getRecipient()->getComputedTitle() . ' (' . $this->bean->getRecipient()->getEmail() . ')'));
            if ($this->bean->isInvoice() 
                    && $this->bean->getStatus() === InvoiceBean::STATUS_CREATED 
                    && !I::isLevelBeginner() // Il existe un message plus long pour les débutants...
                    && !I::isLevelExpert()) {
                $htmlBefore .= (string) H::msg(sprintf(__("<strong>Attention</strong> : une fois envoyé à son destinataire, cette %s ne sera plus modifiable."), $this->bean->getTypeName()))->statusWarning();
            }
            $this->setHtmlBefore($htmlBefore);
        }
        
        $templates = LTM::getTemplates('invoice', ['status_created']);
        if (!$templates) {
            throw new ArchException('Aucun template disponible pour envoyer des documents !');
        }
        
        $this->add((new ElementSelect('template'))
                ->setOptions($templates)
                ->setValue(array_keys($templates)[0])
                ->setLabel(__("Modèle à utiliser"))
                );
        
        $this->add((new ElementSubmit('submit'))->setValue(__("Envoyer")));
    }
}
