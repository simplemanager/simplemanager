<?php
namespace App\Account\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Filter\Filter as F;
use Osf\Validator\Validator as V;
use Osf\Form\Element\ElementReset;
use DB;

/**
 * Sequences updates
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormSequences extends Form
{
    public function init()
    {
        $this->setTitle(__("Séquences"), 'edit')
             ->setStarsForRequired(false)
             ->setHelp('form-sequences');
        
        $vMax = V::newGreaterThan(0)->setMessage(__("Une séquence est obligatoirement un entier positif."), 'notGreaterThan');
        $vMin = V::newLessThan(99999)->setMessage(__("Cette valeur est trop grande. Utilisez des numérotations qui se réinitialisent tous les mois si vous générez de nombreux documents."), 'notLessThan');
        
        $this->add((new ElementInput('seq_quote'))
                ->setTypeNumber()
                ->setValue(DB::getSequenceTable()->nextValue('quote', null, false))
//                ->setAddonLeft(null, 'sort-numeric-asc')
                ->setRequired(true)
                ->add(F::newToInt())
                ->add(V::newIsInt())
                ->add($vMax)
                ->add($vMin)
//                ->getHelper()->setSize(4)->getElement()
                ->setLabel(__("Devis :")));
        
        $this->add((new ElementInput('seq_order'))
                ->setTypeNumber()
                ->setValue(DB::getSequenceTable()->nextValue('order', null, false))
//                ->setAddonLeft(null, 'sort-numeric-asc')
                ->setRequired(true)
                ->add(F::newToInt())
                ->add(V::newIsInt())
                ->add($vMax)
                ->add($vMin)
//                ->getHelper()->setSize(4)->getElement()
                ->setLabel(__("Commande :")));
        
        $this->add((new ElementInput('seq_invoice'))
                ->setTypeNumber()
                ->setValue(DB::getSequenceTable()->nextValue('invoice', null, false))
//                ->setAddonLeft(null, 'sort-numeric-asc')
                ->setRequired(true)
                ->add(F::newToInt())
                ->add(V::newIsInt())
                ->add($vMax)
                ->add($vMin)
//                ->getHelper()->setSize(4)->getElement()
                ->setLabel(__("Facture :")));
        
        $this->add((new ElementReset('cancel'))->setValue(__("Réinitialiser")));
        $this->add((new ElementSubmit('submit'))->setValue(__("Mettre à jour")));
    }
    
    public function isValid($values = null) {
        $valid = parent::isValid($values);
        return $valid;
    }
}
