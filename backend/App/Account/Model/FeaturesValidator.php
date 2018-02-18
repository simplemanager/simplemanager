<?php
namespace App\Account\Model;

use Osf\Form\Validator\FormValidatorInterface;
use Osf\Form\Element\ElementAbstract;
use Osf\Form\Element\ElementInput;
use Osf\Form\AbstractForm;
use Sma\Session\Identity as I;
use Sma\Db\CompanyTable;
use H;

/**
 * Validations additionnelles de la configuration générale
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage account
 */
class FeaturesValidator implements FormValidatorInterface
{
    protected $hasWarnings = false;
    protected $hasErrors = false;
    
    /**
     * @param AbstractForm $form
     * @return bool
     */
    public function isValid(AbstractForm $form): bool
    {
        $this->hasWarnings = false;
        $this->hasErrors = false;
        $values = $form->getValues();
        
        $this->validateCompanyInfos($form, $values)
             ->validateRib($form, $values)
             ->validateInvoiceIntegrity($form, $values);
        
        // $form->getSubForm('company')->setHtmlBefore(H::msg('Un petit chose là....', 'warning'));
        
        return !$this->hasErrors();
    }
    
    /**
     * Informations générales sur l'entreprise
     * @param AbstractForm $form
     * @param array $values
     * @return $this
     */
    protected function validateCompanyInfos(AbstractForm $form, array $values)
    {
        $company = $values['company'];
        
        // Siret != TVA intra
        if (isset($company['siret']) && $company['siret'] 
        &&  isset($company['tva_intra']) && $company['tva_intra']
        && (substr($company['siret'], 0, 9) !== substr($company['tva_intra'], 4))) {
            $this->addError(
                    $form->getSubForm('company')->getElement('siret'), 
                    __("Ce siret ne correspond pas à votre TVA intracommunautaire."))
                ->addError(
                    $form->getSubForm('company')->getElement('tva_intra'), 
                    __("Ce n° de TVA ne correspond pas à votre siret, l'un des deux doit être corrigé."));
        }
        
        // Personne morale sans SIRET
        if (!in_array(I::getCompany()['legal_status'], ['ei', 'a']) && !$company['siret']) {
            $warn = sprintf(__("Le siret/siren de votre %s doit obligatoirement apparaître dans vos factures. Il est vivement conseillé de le mentionner."), $this->getIdentityLegalStatus());
            $this->addWarning($form->getSubForm('company')->getElement('siret'), $warn);
        }
        
        // Personne morale sans RCS
        if (!in_array(I::getCompany()['legal_status'], ['ei', 'a']) && !$company['rcs']) {
            $warn = sprintf(__("Le RCS de votre %s doit apparaître dans vos factures, nous vous conseillons de le mentionner ici."), $this->getIdentityLegalStatus());
            $this->addWarning($form->getSubForm('company')->getElement('rcs'), $warn);
        }
        
        // Association sans RNA / Préfecture
        if (I::getCompany()['legal_status'] === 'a') {
            if (!$company['rna']) {
                $warn = sprintf(__("Le numéro RNA de votre association doit obligatoirement apparaître dans vos factures. Nous vous conseillons de le mentionner ici."), $this->getIdentityLegalStatus());
                $this->addWarning($form->getSubForm('company')->getElement('rna'), $warn);
            }
            if (!$company['prefecture']) {
                $warn = sprintf(__("Le nom de la préfecture ou sous-préfecture où vous avez déclaré votre association doit obligatoirement apparaître dans vos factures."), $this->getIdentityLegalStatus());
                $this->addWarning($form->getSubForm('company')->getElement('prefecture'), $warn);
            }
        }
        
        return $this;
    }
    
    /**
     * RIB
     * @param AbstractForm $form
     * @param array $values
     * @return $this
     */
    protected function validateRib(AbstractForm $form, array $values)
    {
        $company = $values['company'];
        
        // RIB incomplet
        if (($company['rib_owner'] || $company['rib_domicil'] || $company['rib_iban'] || $company['rib_bic'])
        && !($company['rib_owner'] && $company['rib_domicil'] && $company['rib_iban'] && $company['rib_bic'])) {
            $warn = __("RIB incomplet, il ne peut être affiché dans vos factures.");
            foreach (['rib_owner', 'rib_domicil', 'rib_iban', 'rib_bic'] as $key) {
                $elt = $form->getSubForm('company')->getElement($key);
                if ($elt->getValue() === '') {
                    $this->addWarning($elt, $warn);
                }
            }
        }
        
        return $this;
    }
    
    /**
     * Intégrité et conformité de la facture
     * @param AbstractForm $form
     * @param array $values
     * @return $this
     */
    protected function validateInvoiceIntegrity(AbstractForm $form, array $values)
    {
        $invoice = $values['invoice'];
        
        // Clause de pénalités
        if (trim($invoice['penal_phrase']) === '') {
            $this->addError(
                    $form->getSubForm('invoice')->getElement('penal_phrase'), 
                    __("La mention relative aux pénalités de retard est obligatoire."));
        } else {
            $cpt = substr_count($invoice['penal_phrase'], '[rate]');
            if ($cpt === 0) {
                $this->addError(
                        $form->getSubForm('invoice')->getElement('penal_phrase'), 
                        __("Le mot clé [rate] doit apparaître dans votre phrase pour y intégrer le taux de pénalité choisi."));
            } else if ($cpt > 1) {
                $this->addError(
                        $form->getSubForm('invoice')->getElement('penal_phrase'), 
                        __("Le mot clé [rate] ne peut apparaître plus d'une fois dans votre phrase."));
            }
        }
        
        // Format des codes
        $this->validateCode($form->getSubForm('invoice')->getElement('code_quote'), $invoice['code_quote'])
             ->validateCode($form->getSubForm('invoice')->getElement('code_order'), $invoice['code_order'])
             ->validateCode($form->getSubForm('invoice')->getElement('code_invoice'), $invoice['code_invoice']);
        
        // Codes identiques
        if ($invoice['code_quote'] === $invoice['code_order']) {
            $this->addError($form->getSubForm('invoice')->getElement('code_quote'), 
                    __("Ne peut avoir le même code que la commande"));
            $this->addError($form->getSubForm('invoice')->getElement('code_order'), 
                    __("Ne peut avoir le même code que le devis"));
        }
        if ($invoice['code_quote'] === $invoice['code_invoice']) {
            $this->addError($form->getSubForm('invoice')->getElement('code_quote'), 
                    __("Ne peut avoir le même code que la facture"));
            $this->addError($form->getSubForm('invoice')->getElement('code_invoice'), 
                    __("Ne peut avoir le même code que le devis"));
        }
        if ($invoice['code_order'] === $invoice['code_invoice']) {
            $this->addError($form->getSubForm('invoice')->getElement('code_order'), 
                    __("Ne peut avoir le même code que la facture"));
            $this->addError($form->getSubForm('invoice')->getElement('code_invoice'), 
                    __("Ne peut avoir le même code que la commande"));
        }
        
        // Délai de paiement
        if ($invoice['delay_type'] !== 'delay' && $invoice['delay'] !== '') {
            $this->addWarning(
                    $form->getSubForm('invoice')->getElement('delay'), 
                    __("Compte tenu du délai de paiement choisi, cette valeur est inutile."));
        } else if ($invoice['delay_type'] === 'delay' && $invoice['delay'] > 60) {
            $this->addWarning(
                    $form->getSubForm('invoice')->getElement('delay'), 
                    __("En France, le délai de paiement ne peut être supérieur à 60 jours, sauf dérogation."));
        }
        
        // Mentions obligatoires sur les factures
        switch (I::getCompany()['legal_status']) {
            case 'ei' : 
                $required = ['user', 'address'];
                break;
            case 'a' : 
                $required = ['title', 'intro', 'address'];
                break;
            default : 
                $required = ['title', 'intro', 'address', 'siren', 'siret', 'rcs'];
        }
        $fields = ['header', 'footer1', 'footer2', 'footer3'];
        foreach ($fields as $field) {
            $required = array_diff($required, $values['document'][$field]);
        }
        
        // OU logique entre siren et siret (à améliorer ?)
        $rflip = array_flip($required);
        if (isset($rflip['siren']) && isset($rflip['siret'])) {
            $rflip['siret'] = true;
        } else {
            unset($rflip['siret']);
        }
        unset($rflip['siren']);
        $required = array_keys($rflip);
        
        // Messages à afficher si nécessaire
        if (!empty($required)) {
            $legalStatus = $this->getIdentityLegalStatus();
            $elts = [
                'title'   => sprintf(__("la dénomination de votre %s (Nom Société)"), $legalStatus),
                'user'    => __("votre prénom et nom (Prénom Nom)"),
                'intro'   => __("votre statut (Statut & Infos)"),
                'address' => I::getCompany()['legal_status'] === 'ei' ? __("votre adresse") : __("l'adresse de votre siège social"),
                'siret'   => __("votre numéro siren ou siret"),
                'rcs'     => __("votre numéro RCS")
            ];
            $list = H::htmlList();
            foreach ($required as $eltKey) {
                $list->addItem($elts[$eltKey]);
            }
            $msg = sprintf(__("Les factures de votre %s doivent aussi comporter des éléments suivants :"), $legalStatus) . $list;
            $form->getSubForm('document')->setHtmlBefore(H::callout(__("Mention(s) obligatoire(s) manquante(s)"), $msg, 'warning'));
            foreach ($fields as $field) {
                $this->addWarning(
                        $form->getSubForm('document')->getElement($field), 
                        __("Emplacement possible pour les informations manquantes mentionnées ci-dessus."));
            }
        }

        return $this;
    }
    
    /**
     * Validation de code de document
     * @param ElementInput $elt
     * @param string $value
     * @return $this
     */
    protected function validateCode(ElementInput $elt, string $value)
    {
        if ($value === '') {
            $this->addError($elt, __("Cette valeur est obligatoire"));
            return $this;
        }
        if (!preg_match('/^[a-zA-Z_-]+$/', preg_replace('/\[[^]]+\]/', '', $value))) {
            $this->addError($elt, __("Seuls les caractères alpha, _ et - sont autorisés"));
        }
        
        $tags = [];
        preg_match_all('/\[([^]]+)\]/', $value, $tags);
        $existTags = [];
        foreach ($tags[1] as $tag) {
            if (in_array($tag, ['nnn', 'nnnn', 'nnnnn', 'nnnnnn', 'yy', 'yyyy', 'mm'])) {
                $existTags[$tag] = isset($existTags[$tag]) ? $existTags[$tag] + 1 : 1;
            } else {
                $this->addError($elt, sprintf(__("Le mot clé %s n'existe pas"), '[' . $tag . ']'));
            }
        }
        foreach($existTags as $key => $cpt) {
            if ($cpt > 1) {
                $this->addError($elt, sprintf(__("Le mot clé [%s] doit apparaître une seule fois"), $key));
            }
        }
        if (!isset($existTags['nnn']) && !isset($existTags['nnnn']) && !isset($existTags['nnnnn']) && !isset($existTags['nnnnnn'])) {
            $this->addError($elt, __("Vous devez obligatoirement spécifier le mot clé [nnnn] correspondant à la numérotation. Vous pouvez mettre entre 3 et 6 'n'."));
        }
        if (((isset($existTags['nnn']) ? 1 : 0)
           + (isset($existTags['nnnn']) ? 1 : 0)
           + (isset($existTags['nnnnn']) ? 1 : 0)
           + (isset($existTags['nnnnnn']) ? 1 : 0)) > 1) {
            $this->addError($elt, __("Vous ne devez pas spécifier plusieurs fois le numéro de facture dans le même code."));
        }
        if (isset($existTags['yy']) && isset($existTags['yyyy'])) {
            $this->addError($elt, __("Ne spécifiez pas [yy] et [yyyy] dans le même code."));
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    protected function getIdentityLegalStatus(): string
    {
        return CompanyTable::STATUS_TITLES_SHORT[I::getCompany()['legal_status']];
    }
    
    /**
     * Ajoute un warning
     * @param ElementAbstract $elt
     * @param string $msg
     * @return $this
     */
    protected function addWarning(ElementAbstract $elt, string $msg)
    {
        $elt->addWarning($msg);
        $this->hasWarnings = true;
        return $this;
    }
    
    /**
     * Ajoute une erreur
     * @param ElementAbstract $elt
     * @param string $msg
     * @return $this
     */
    protected function addError(ElementAbstract $elt, string $msg)
    {
        $elt->addError($msg);
        $this->hasErrors = true;
        return $this;
    }
    
    /**
     * @return bool
     */
    public function hasWarnings(): bool
    {
        return $this->hasWarnings;
    }
    
    /**
     * @return bool
     */
    public function hasErrors(): bool
    {
        return $this->hasErrors;
    }
}
