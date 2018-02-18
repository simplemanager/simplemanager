<?php
namespace App\Product\Form;

use Osf\Form\OsfForm as Form;
use Sma\Session\Identity as I;
use Osf\Stream\Text;
use Osf\Form\Element\ElementAbstract;
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
class FormProduct extends Form
{
    protected $oldCodeValue;
    
    public function init()
    {
        $form = DB::getProductTable()->getForm()->displayLabels(false);
        $form->getElement('submit')->getHelper()->addCssClass('hidden');
        $form->getElement('code')->setRequired(false);
//        $form->getElement('title')->setFocus();
        
        // Si on gère des taxes (TVA) alors on récupère les valeurs par défaut dans la conf
        if (I::hasTax()) {
            $form->getElement('tax')->setValue(self::getDefaultTax());
            $form->getElement('price_type')->setValue(self::getHtOrTtc());
        } else {
            $form->removeElement('tax');
            $form->removeElement('price_type');
        }
        
        foreach ($form->getElements() as $elt) {
            $this->add($elt);
        }
    }
    
    public function oldCodeValue($codeValue)
    {
        $this->oldCodeValue = $codeValue;
    }
    
    public function isValid($values = null)
    {
        $valid = parent::isValid($values);
        if (!$valid) {
            return false;
        }
        
        $codeElt = $this->getElement('code');
        if ($codeElt->getValue() === '') {
            $title = $this->getElement('title')->getValue();
            self::nextProductCode($title, $codeElt);
        } else if ($this->oldCodeValue !== $codeElt->getValue()
                && $this->productCodeExists($codeElt->getValue())) {
            $codeElt->addError(__("Ce code produit existe déjà."));
            $valid = false;
        }
        return $valid;
    }
    
    /**
     * % de TVA par défaut des produits
     * @return float
     */
    public static function getDefaultTax()
    {
        $defaultTax = I::getParam('product', 'defaultTax');
        return $defaultTax !== null ? (float) $defaultTax : 20;
    }
    
    /**
     * Prix spécifiés par défaut ht ou ttc ?
     * @return string ttc or ht
     */
    public static function getHtOrTtc()
    {
        return I::getParam('product', 'withTax') ? 'ttc' : 'ht';
    }
    
    /**
     * Quel est le code du prochaine produit portant le titre donné ?
     * @param string $title
     * @param ElementAbstract $codeElt
     * @return boolean
     */
    public static function nextProductCode($title, ElementAbstract $codeElt = null)
    {
        $prefix = Text::toUpper(mb_substr(Text::getAlpha($title), 0, 3));
        $prefix = $prefix ?: 'PRD';
        $sql ='SELECT MAX(code) as `maxcode` FROM `product` '
                . 'WHERE `product`.`id_account`=' . (int) I::getIdAccount() . ' '
                . 'AND `product`.`code` REGEXP \'^' . $prefix . '[0-9]{4}$\'';
        $row = DB::getProductTable()->execute($sql)->current();
        if ($row && isset($row['maxcode'])) {
            $index = substr($row['maxcode'], 3) + 1;
            if ($index > 9999) {
                if ($codeElt) {
                    $codeElt->addError(__("Valeur maximale atteinte. Saisissez le code manuellement."));
                    $codeElt->setValue($row['maxcode']);
                }
                unset($index);
            }
        } else {
            $index = 1;
        }
        if (isset($index)) {
            $code = sprintf($prefix . "%'04d", $index);
            if ($codeElt) {
                $codeElt->setValue($code);
            }
            return $code;
        }
        return false;
    }
    
    protected function productCodeExists($code)
    {
        return (bool) DB::getProductTable()->select(['code' => $code, 'id_account' => I::getIdAccount()])->count();
    }
}
