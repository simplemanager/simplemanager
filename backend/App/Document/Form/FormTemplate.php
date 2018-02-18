<?php
namespace App\Document\Form;

use Osf\Pdf\Document\Bean\ContactBean;
use Osf\Form\Element\ElementSelect;
use Osf\Helper\Tab;
use Sma\Session\Identity as I;
use Sma\Bean\LetterBean;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use App\Document\Form\FormLetter;
use ACL, DB;

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
class FormTemplate extends FormLetter
{
    const TEMPLATE_SPECIFIC_FIELDS = ['title', 'data_type', 'data_type_filters', 'target_type', 'category', 'description'];
    
    protected $bean;
    
    protected function expert()
    {
        return I::isLevelExpert();
    }
    
    public function init()
    {
        $this->setTitle(__("Modèle de lettre"), 'envelope', 'fuchsia');
        $this->setHelp('form-template');
        $ltForm = DB::getLetterTemplateTable()->getForm()->displayLabels(false);
        
        // Champs spécifiques au modèles
        
        $this->add($ltForm->getElement('title')
                ->setLabel($this->expert() ? null : __("Titre du modèle"))
                ->setFocus());
        $this->add($ltForm->getElement('description')
                ->setLabel($this->expert() ? null : __("Description"))
                ->setDescription($this->expert() ? null : __("Utilisée uniquement pour information et indexation du moteur de recherche."))
                ->setRelevanceLow());
        
        if (ACL::isAdmin()) {
            $categories = DB::getLetterTemplateTable()->getCategories();
            $this->add((new ElementSelect('category'))
                ->setLabel($this->expert() ? null : __("Catégorie"))
                ->allowCreate()
                ->setRequired()
                ->setOptions($categories)
            );
        }
        
        $this->add((new ElementSelect('data_type'))
                ->setLabel($this->expert() ? null : __("Données liées"), 
                           $this->expert() ? null : 'form-template-donnees-liees')
                ->setOptions(LTM::getDataTypeOptions())
                ->setRequired()
            );
        
        $this->add((new ElementSelect('data_type_filters'))
                ->setLabel($this->expert() ? null : __("Filtres (documents liés)"),
                           $this->expert() ? null : 'form-template-filtres')
                ->setOptions(LTM::getDataFiltersOptions())
                ->setMultiple()
                ->setRequired()
            );
        
        $this->add((new ElementSelect('target_type'))
                ->setLabel($this->expert() ? null : __("Optimisé pour"))
                ->setHelpKey('form-template-optim')
                ->setOptions(LTM::getTargetTypeOptions())
                ->setRequired()
            );
        
        // Champs communs Modèle / Lettre
        $this->buildCommonFields();
        
        // Modification des champs communs
        $this->getElement('body')
                ->setPlaceholder(__("Contenu du modèle.") 
                    . " \n\n" 
                    . __("Ne pas inclure l'introduction (M./Mme xxx,). Vous pouvez utiliser Markdown et les tags de substitution relatifs aux données liées."));
    }
    
    /**
     * Filtre les valeurs destinées au bean
     * @return array
     */
    public function getValuesForLetterBean()
    {
        $values = $this->getValues();
        foreach (self::TEMPLATE_SPECIFIC_FIELDS as $fieldKey) {
            if (isset($values[$fieldKey])) {
                unset($values[$fieldKey]);
            }
        }
        return $values;
    }
    
    /**
     * Récupération des valeurs pour la table des templates + construction du bean
     * @param bool $buildTemplateBean
     * @return array
     */
    public function getValuesForTemplate(bool $buildTemplateBean = true)
    {
        $values = $this->getValues(self::TEMPLATE_SPECIFIC_FIELDS);
        if (!isset($values['category']) || !$values['category']) {
            $values['category'] = 'mine';
        }
        if ($buildTemplateBean) {
            $values['bean'] = serialize($this->buildTemplateBean());
        }
        $values['data_type_filters'] = implode(',', $values['data_type_filters']);
        return $values;
    }
    
    /**
     * Construit le bean LetterBean du template à partir des valeurs
     * @return LetterBean
     */
    public function buildTemplateBean(ContactBean $recipient = null)
    {
        // $recipient = $recipientBean ?? new ContactBean();
        $values = $this->getValuesForLetterBean();
        if ($recipient) {
            $values['recipient_bean'] = $recipient;
        }
        $values = Tab::reduce($values, [], self::TEMPLATE_SPECIFIC_FIELDS);
        return (new LetterBean(null, $recipient !== null))->populate($values);
    }
}
