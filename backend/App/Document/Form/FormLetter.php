<?php
namespace App\Document\Form;

use Osf\Form\OsfForm as Form;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementCheckbox;
use Osf\Form\Element\ElementSelect;
use Osf\Form\Element\ElementHidden;
use Osf\Form\Element\ElementTextarea;
use Osf\Form\Element\ElementSubmit;
use Osf\Form\Hydrator\HydratorAbstract;
use Osf\Filter\Filter as F;
use Osf\Validator\Validator as V;
use App\Recipient\Model\RecipientDbManager as RM;
use Sma\Session\Identity as I;
use Sma\Form\Addon\FocusRecipient;
use Sma\Bean\InvoiceBean;
use Sma\Bean\LetterBean;
use Sma\Db\DocumentRow;
use Sma\Bean\ContactBean;
use App\Document\Model\DocumentDbManager;
use L, DB;

/**
 * Lettre libre
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormLetter extends Form
{
    use FocusRecipient;
    
    protected $bean;
    
    public function __construct(bool $focusRecipient = false)
    {
        $this->focusRecipient = $focusRecipient;
        parent::__construct();
    }
    
    protected function expert()
    {
        return I::isLevelExpert();
    }
    
    public function init()
    {
        $this->setTitle(__("Lettre libre"), L::ICON_LETTER);
        
        $this->add((new ElementSelect('recipient'))
            ->setLabel($this->expert() ? null : __("Destinataire"))
            ->setRequired()
            ->setAutocompleteAdapter(new RM($this->focusRecipient ? 0 : null))
            // ->setAddonRight(null, 'plus', ['onclick' => "alert('Fonctionnalité à développer');"])
        );
        $this->registerFocusRecipient($this->getElement('recipient'));

        // Document joint
        $this->add((new ElementSelect('attachment_id'))
            ->setLabel($this->expert() ? null : __("et/ou document attaché"))
            ->setHelpKey($this->expert() ? null : 'lettre-piece-jointe')
            ->setRelevanceLow()
            ->setRequired(false)
            ->setAutocompleteAdapter(new DocumentDbManager(DocumentDbManager::TYPE_INVOICES))
        );
        
        $this->buildCommonFields();
    }
    
    /**
     * Champs communs entre lettre et modèle
     */
    protected function buildCommonFields()
    {
        //$this->add((new ElementInput('dear'))
        //        ->setPlaceholder('Cher xxx'));
        
        $this->add((new ElementInput('object'))
                ->setLabel($this->expert() ? null : __("Objet ou sujet"))
                ->setTooltip($this->expert() ? null : __("Objet de la lettre ou sujet de l'email"))
                ->setHelpKey('lettre-objet')
                ->setRequired()
                ->setPlaceholder(__("Objet / Sujet")));
        
        
        $this->add((new ElementInput('k_libs_1'))
                ->setLabel($this->expert() ? null : __("Libellé (1)"))
                ->add(F::getStringTrim())
                ->add(V::newStringLength(2, 40))
                ->setPlaceholder(__("Ex: A l'attention de"))
                ->setRelevanceLow()
                ->getHelper()->setSize(4)->getElement()
            );

        $this->add((new ElementInput('v_libs_1'))
                ->setLabel($this->expert() ? null : __("Texte du libellé (1)"))
                ->add(F::getStringTrim())
                ->add(V::newStringLength(1, 60))
                ->setRelevanceLow()
                ->getHelper()->setSize(8)->getElement()
            );
        
        $this->add((new ElementInput('k_libs_2'))
                ->setLabel($this->expert() ? null : __("Libellé (2)"))
                ->add(F::getStringTrim())
                ->add(V::newStringLength(2, 40))
                ->setPlaceholder(__("Ex: V/réf."))
                ->setRelevanceLow()
                ->getHelper()->setSize(4)->getElement()
            );

        $this->add((new ElementInput('v_libs_2'))
                ->setLabel($this->expert() ? null : __("Texte du libellé (2)"))
                ->add(F::getStringTrim())
                ->add(V::newStringLength(1, 60))
                ->setRelevanceLow()
                ->getHelper()->setSize(8)->getElement()
            );
        
        $this->add((new ElementInput('dear'))
                ->setLabel($this->expert() ? null : __("M. ou Mme XXX"))
                ->setPlaceholder(__("Cher XXX, (vide = automatique)"))
                ->setDescription($this->expert() ? null : __("Placé juste avant le contenu. Laissez ce champ vide pour générer automatiquement cette valeur en fonction du destinataire choisi."))
                ->add(F::getStringTrim())
                ->add(V::newStringLength(1, 30))
                ->setRelevanceLow()
            );
        
        $this->add((new ElementTextarea('body'))
                ->setLabel(I::isLevelBeginner() || I::isLevelExpert() ? null : __("Contenu du message"), 'markdown')
                ->setPlaceholder(__("Contenu de la lettre.") . " \n\n" . __("Ne pas inclure l'introduction (M./Mme xxx,) et la signature, qui sont générés automatiquement."))
                ->setRequired()
                ->add(V::newStringLength(1, 4000))
                ->getHelper()
                    ->setAttribute('rows', 5)
                    ->getElement()
            );
        
//        $this->add((new ElementCheckbox('attach_letter'))
//                ->setLabel(__("Envoyer la lettre en pièce jointe"))
//                ->setDescription($this->expert() ? null : __("Si vous générez un e-mail avec le contenu de cette lettre, la version PDF de la lettre sera ajoutée en pièce jointe."))
//                ->setValue(0)
//                ->setRelevanceLow()
//            );
        
        $this->add((new ElementCheckbox('confidential'))
                ->setLabel(__("Ajouter la mention « document confidentiel »"))
                ->setValue(I::getParam('document', 'confidential') ? 1 : 0)
                ->setRelevanceLow()
                );
        
//        $this->add((new ElementCheckbox('markdown'))
//                ->setLabel(__("Activer le formattage Markdown"))
//                ->setDescription(__("Seul le contenu de la lettre est formatté si vous activez cette option."))
//                ->setHelpKey('markdown')
//                ->setValue(1)
//                ->setRelevanceLow()
//            );
        
        $this->add((new ElementHidden('action'))->setIgnore());
        
        $this->add((new ElementSubmit('preview'))
                ->setValue(__("Aperçu"))
                ->getHelper()->setAttribute('onclick', '$(\'#action\').val(\'preview\');')->getElement());
        $this->add((new ElementSubmit('save'))
                ->setValue(__("Enregistrer"))
                ->getHelper()->setAttribute('onclick', '$(\'#action\').val(\'save\');')->getElement()
                );
        
//        $this->add((new ElementSubmit('envoyer'))->setValue(__("Envoyer")));
    }
    
    public function setBean(LetterBean $bean, $values = [])
    {
        $this->bean = $bean;
        $values['object'] = $bean->getLibs()['Objet :'] ?: $bean->getSubject();
        $values['body'] = $bean->getBody();
        $values['confidential'] = (int) $bean->getConfidential();
        $values['attachment_id'] = $bean->getAttachmentId();
        $values['dear'] = $bean->getDear(false);
//        $values['attach_letter'] = (int) $bean->getAttachLetter();
        $cpt = 1;
        foreach ($bean->getLibs() as $key => $value) {
            if ($key === 'Objet :') { continue; }
            $values['k_libs_' . $cpt] = rtrim($key, ' :');
            $values['v_libs_' . $cpt] = $value;
            $cpt++;
        }
        if ($this->getElement('recipient')) {
            $values['recipient'] = $bean->getRecipient()->getIdCompany();
        }
        $this->hydrate($values, null, true, true);
    }
    
    /**
     * Manipulations post-hydratation
     * @param array $values
     * @param HydratorAbstract $hydrator
     * @param bool $prefixedValues
     * @param bool $noError
     * @param bool $fullValues
     * @return $this
     */
    public function hydrate(?array $values, HydratorAbstract $hydrator = null, bool $prefixedValues = true, bool $noError = false, bool $fullValues = false)
    {
        // Si seul un document a été spécifié sans destinataire, on va chercher le 
        // destinataire du document pour remplir le destinataire de la lettre. 
        if ((!isset($values['recipient']) || !$values['recipient']) 
        &&  (isset($values['attachment_id']) && $values['attachment_id'])) {
            $doc = DB::getDocumentTable()->findSafe((int) $values['attachment_id']);
            if ($doc instanceof DocumentRow
                    && $doc->getBean() instanceOf InvoiceBean
                    && $doc->getBean()->getRecipient() instanceof ContactBean
                    && $doc->getBean()->getRecipient()->getId()) {
                $values['recipient'] = (string) $doc->getBean()->getRecipient()->getIdCompany();
            }
        }
        
        return parent::hydrate($values, $hydrator, $prefixedValues, $noError, $fullValues);
    }
    
    /**
     * @return \Sma\Bean\LetterBean
     */
    public function getBean()
    {
        return $this->bean;
    }
}
