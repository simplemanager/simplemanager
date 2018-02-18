<?php
namespace App\Document\Form;

use Osf\Exception\DisplayedException;
use Osf\Form\Element\ElementSelect;
use Osf\Form\Element\ElementSubmit;
use Osf\Form\Element\ElementHidden;
use Osf\Form\OsfForm as Form;
use Sma\Db\LetterTemplateRow;
use Sma\Session\Identity as I;
use App\Recipient\Model\RecipientDbManager as RM;
use App\Document\Model\DocumentDbManager as DM;
use Osf\Form\Hydrator\HydratorAbstract;

/**
 * Modèle de lettre
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormGenerator extends Form
{
    const COUNT_LIMIT = 10;
    
    /**
     * @var LetterTemplateRow
     */
    protected $tpl;

    public function __construct(LetterTemplateRow $template)
    {
        $this->tpl = $template;
        parent::__construct();
    }
    
    protected function expert()
    {
        return I::isLevelExpert();
    }
    
    public function init()
    {
        $this->setTitle($this->tpl->getTitle(), 'envelope', 'fuchsia');
        // $this->setHelp('form-template');
        
        $target = (new ElementSelect('target'))
                ->setMultiple(true, self::COUNT_LIMIT)
                ->setRequired()
                ->setValue([]);
        switch ($this->tpl->getDataType()) {
            case 'recipient' : 
                $target->setLabel(__("Destinataires"));
                $target->setTooltip(__("Destinataires du ou des courriers"));
                $target->setAutocompleteAdapter(new RM());
                break;
            default : 
                throw new DisplayedException(__("Générez des courriers liés à des documents depuis la section 'Devis', 'Commandes' ou 'Factures'."));
//                $target->setLabel(__("Documents"));
//                $target->setTooltip(__("Documents contenant les données"));
//                $target->setAutocompleteAdapter(new DM($this->tpl->getDataType()));
        }
        $target->setDescription(sprintf(__("Vous pouvez spécifier jusqu'à %d éléments. L'aperçu est généré avec le premier élément."), self::COUNT_LIMIT));
        $this->add($target);

//        $this->add((new ElementSelect('todo'))
//                ->setLabel(__("Action :"))
//                ->setRequired()
//                ->setOptions([
//                    'save' => __("Créer un document et enregistrer"),
//                    'maildoc' => __("Créer un document et l'envoyer par e-mail"),
//                    'mail' => __("Envoyer un e-mail basé sur le modèle")
//                ])
//                ->setValue('save')
//            );
        
        $this->add((new ElementHidden('action'))->setIgnore());
        $this->add((new ElementSubmit('preview'))
                ->setValue(__("Aperçu"))
                ->getHelper()->setAttribute('onclick', '$(\'#action\').val(\'preview\');')->getElement());
        $this->add((new ElementSubmit('save'))
                ->setValue(__("Générer"))
                ->getHelper()->setAttribute('onclick', '$(\'#action\').val(\'save\');')->getElement());
    }
    
    /**
     * Vérifications complémentaires
     * @param array $values
     * @return bool
     */
    public function isValid($values = null)
    {
        $valid = parent::isValid($values);
        
        // Aucun élément sélectionné
        if (empty($this->getValue('target'))) {
            $this->getElement('target')->addError(__("Spécifiez au moins un élement."));
            $valid = false;
        }
        
        return $valid;
    }
    
    /**
     * 
     * @param array $values
     * @param HydratorAbstract $hydrator
     * @param bool $prefixedValues
     * @param bool $noError
     * @param bool $fullValues
     * @return $this
     */
    public function hydrate(?array $values, HydratorAbstract $hydrator = null, bool $prefixedValues = true, bool $noError = false, bool $fullValues = false)
    {
        if (isset($values['target']) && is_array($values['target'])) {
            
        }
        return parent::hydrate($values, $hydrator, $prefixedValues, $noError, $fullValues);
    }
}
