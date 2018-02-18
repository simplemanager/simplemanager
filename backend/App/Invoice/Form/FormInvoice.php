<?php
namespace App\Invoice\Form;

use Osf\Form\OsfForm as Form;
use Osf\Filter\Filter as F;
use Osf\Validator\Validator as V;
use Osf\Form\Element\ElementSelect;
use Osf\Form\Element\ElementTextarea;
use Osf\Form\Element\ElementInput;
use Osf\Form\Element\ElementSubmit;
use Osf\Form\Element\ElementHidden;
use Osf\Form\Element\ElementCheckbox;
use Osf\Pdf\Document\Bean\ProductBean;
use App\Recipient\Model\RecipientDbManager as RM;
use App\Product\Model\ProductDbManager as PM;
use App\Document\Model\DocumentDbManager as DM;
use App\Product\Form\FormProduct;
use Osf\View\Component;
use Osf\Exception\ArchException;
use Osf\Helper\DateTime as DT;
use Osf\Stream\Json;
use Osf\Stream\Text as T;
use Osf\Helper\Tab;
use Sma\Form\Addon\FocusRecipient;
use Sma\Session\Identity as I;
use Sma\Bean\InvoiceBean;
use App\Common\Container as C;
use DB, H;

/**
 * Invoice
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 20 nov. 2013
 * @package common
 * @subpackage forms
 */
class FormInvoice extends Form
{
    use FocusRecipient;
    
    protected $type;
    protected $bean;
    protected $oldCodeValue;
    protected $previewCodeNumber;
    protected $products = [];
    protected $dbProducts = [];
    protected $hasErrors = false; // Erreurs détectées dans les produits
    protected $focusRecipient = false;
    protected $documentId; // id de l'invoice s'il s'agit d'une modification, pour vérification des doublons de code
    
    public function __construct(string $type = null, bool $focusRecipient = false)
    {
        if ($type !== null) {
            $this->setType($type);
        }
        $this->setFocusRecipient($focusRecipient);
        parent::__construct();
    }
    
    protected function expert()
    {
        return I::isLevelExpert();
    }
    
    protected function param($key)
    {
        // var_dump(I::getParams('invoice'));
        return I::getParam('invoice', $key);
    }
    
    public function init()
    {
        $type = $this->getType();
        $this->setPreviewCodeNumber(DB::getSequenceTable()->nextValue($type, null, false));
        $isInvoice = $type === InvoiceBean::TYPE_INVOICE;
        
        // DESTINATAIRE
        
        $modalJs = "\$('#mform').modal('show');\$('#medit').html('');\$.ajaxCall('" . H::url('recipient', 'edit', ['from' => 'inv']) . "','#medit',false,true);";
        $this->add((new ElementSelect('recipient'))
            ->setPlaceholder(__("Destinataire"))
            ->allowCreate()
            ->setAutocompleteAdapter(new RM($this->getFocusRecipient() ? 0 : null))
            ->setSelectOnTab()
            ->setAddonLeft(null, 'plus', ['onclick' => $modalJs], ['clickable'])
        );
        $this->registerFocusRecipient($this->getElement('recipient'));
        
        // CODE & DATES
        
        $label = sprintf(__("N° de %s"), InvoiceBean::getTypeNameFromType($type));
        $helpKey = $isInvoice && !$this->expert() ? 'numero-facture' : null;
        $this->add((new ElementInput('code'))
            ->setLabel($this->expert() ? null : $label, $helpKey)
            ->setPlaceholder(sprintf(__("Vide = n° auto (%'04s)"), $this->getPreviewCodeNumber()))
            ->setRelevanceLow()
            ->setAddonLeft(null, 'barcode')
            ->setTooltip($this->expert() ? null : __("Laisser vide pour générer un numéro automatique (conseillé)"))
            ->getHelper()->setSize(4)->getElement()
        );
        
        $label = $isInvoice ? __("Date de facturation") : __("Date d'envoi");
        $helpKey = $isInvoice && !$this->expert() ? 'date-facturation' : null;
        $this->add((new ElementInput('date_sending'))
            ->setLabel($this->expert() ? null : $label, $helpKey)
            ->setTooltip($this->expert() ? null : __("Laisser vide pour utiliser la date d'aujourd'hui"))
            ->setTypeDate()
            ->setPlaceholder(__("Vide = aujourd'hui"))
            ->setRelevanceLow()
            ->setAddonLeft(null, 'paper-plane-o')
            ->getHelper()->setSize(4)->getElement()
        );

        $dvLabel = $isInvoice ? __("Date limite de paiement") : __("Date de validité");
        $helpKey = $isInvoice && !$this->expert() ? 'date-limite-paiement' : null;
        
        if ($isInvoice) {
            $days = is_numeric($this->param('delay')) ? $this->param('delay') : 60;
            $delayTypes = [
                'no'       => __("pas affiché"),
                'cash'     => __("date de facturation (comptant)"),
                'delivery' => __("facturation + 1 semaine (à réception)"),
                'delay'    => $days ? sprintf(__("date de facturation + %d jour%s"), $days, $days > 1 ? 's' : '') : __("date de facturation"),
                'fm45'     => __("fin de mois + 45 jours"),
                '45fm'     => __("45 jours + fin de mois"),
                'periodic' => __("date de facturation + 45 jours (périodique)")
            ];
            $emptyValidityMsg = $delayTypes[$this->param('delay_type')];
        } else {
            $emptyValidityMsg = $this->param('delay_other') ? sprintf(__("date d'envoi + %d jour%s"), $this->param('delay_other'), $this->param('delay_other') > 1 ? 's' : '') : __("date d'envoi");
        }
        $placeholder = sprintf(__("Vide = %s"), $emptyValidityMsg);
        
        $this->add((new ElementInput('date_validity'))
            ->setLabel($this->expert() ? null : $dvLabel, $helpKey)
            ->setTooltip($this->expert() ? null : $placeholder)
            ->setPlaceholder($placeholder)
            ->setTypeDate()
            ->setRelevanceLow()
            ->setAddonLeft(null, 'hand-stop-o')
            ->getHelper()->setSize(4)->getElement()
        );
        
        // LIBELLES
        
        $libsKeys = array_keys(InvoiceBean::getLibSelectList());
        $this->add((new ElementSelect('k_libs_1'))
            ->setLabel($this->expert() ? null : __("Libellé (1)"))
            ->setOptions(InvoiceBean::getLibSelectList())
            ->allowCreate()
            ->setRelevanceLow()
            ->getHelper()->setSize(4)->getElement()
        );

        $this->add((new ElementInput('v_libs_1'))
            ->setLabel($this->expert() ? null : __("Texte du libellé (1)"))
            ->add(F::getStringTrim())
            ->add(V::getStringLength(1, 60))
            ->setRelevanceLow()
            ->setValue($this->param('object'))
            ->getHelper()->setSize(8)->getElement()
        );
        
        $this->add((new ElementSelect('k_libs_2'))
            ->setLabel($this->expert() ? null : __("Libellé (2)"))
            ->setOptions(InvoiceBean::getLibSelectList())
            ->setValue($libsKeys[3])
            ->setRelevanceLow()
            ->getHelper()->setSize(4)->getElement()
        );

        $this->add((new ElementInput('v_libs_2'))
            ->setLabel($this->expert() ? null : __("Texte du libellé (2)"))
            ->add(F::getStringTrim())
            ->add(V::getStringLength(1, 60))
            ->setRelevanceLow()
            ->getHelper()->setSize(8)->getElement()
        );
        
        $this->add((new ElementSelect('k_libs_3'))
            ->setLabel($this->expert() ? null : __("Libellé (3)"))
            ->setOptions(InvoiceBean::getLibSelectList())
            ->setValue($libsKeys[2])
            ->setRelevanceLow()
            ->getHelper()->setSize(4)->getElement()
        );

        $this->add((new ElementInput('v_libs_3'))
            ->setLabel($this->expert() ? null : __("Texte du libellé (3)"))
            ->add(F::getStringTrim())
            ->add(V::getStringLength(1, 60))
            ->setRelevanceLow()
            ->getHelper()->setSize(8)->getElement()
        );
        
        // BEFORE AFTER
        
        $this->add((new ElementTextarea('md_before'))
            ->setLabel($this->expert() ? null : __("Texte d'introduction"))
            ->setPlaceholder($this->expert() ? __("Introduction") : __("Ce texte sera ajouté avant la liste des produits"))
            ->setRelevanceLow()
            ->setValue($this->param('intro'))
        );
        
        $this->add((new ElementTextarea('md_after'))
            ->setLabel($this->expert() ? null : __("Texte de fin de document"))
            ->setPlaceholder($this->expert() ? __("Conclusion") : __("Ce texte sera ajouté après les totaux"))
            ->setRelevanceLow()
            ->setValue($this->param('conclu'))
        );
        
        // Facture d'avoir et document lié
        
        if ($this->getType() === InvoiceBean::TYPE_INVOICE) {
            $this->add((new ElementCheckbox('credit'))
                ->setLabel(__("Facture d'avoir (note de crédit)"))
                ->setDescription($this->expert() ? null : __("Si vous cochez cette case il est conseillé de spécifier ci-dessous la facture qui fait l'objet d'un remboursement."))
                ->setRelevanceLow()
                ->setHelpKey('avoir')
            );
        }
        
        $this->add((new ElementSelect('id_document_linked'))
            ->setLabel(__("Document lié"))
            ->setAutocompleteAdapter(new DM(DM::TYPE_INVOICES))
            ->setRelevanceLow()
            ->setHelpKey('document-lie')
        );
        
        // Facturation HT hors franchise
        if (I::hasTax()) {
            
            $this->add((new ElementSelect('display_tax'))
                    ->setLabel(__("Facture HT ou TTC ?"))
                    ->setOptions([
                        'auto' => __("Automatique : HT/TTC en fonction du destinataire"),
                        'no'   => __("Facturer HT (sans la TVA)"),
                        'yes'  => __("Facturer avec la TVA")
                    ])
                    ->setValue('auto')
                    ->setRelevanceLow()
                    ->setHelpKey('display-tax')
            );

            $this->add((new ElementInput('no_tax_article'))
                    ->setLabel(__("Facture HT : mention légale"))
                    ->setRelevanceLow()
                    ->setHelpKey('no-tax-article')
                    ->add(V::newStringLength(10, 50))
            );
        }
        
        // SUBMIT
        
        $this->add((new ElementHidden('action'))->setIgnore());
        $this->add((new ElementSubmit('preview'))
            ->setValue(__("Aperçu"))
            ->getHelper()->setAttribute('onclick', '$(\'#action\').val(\'preview\');')->getElement());
        $this->add((new ElementSubmit('save'))
            ->setValue(__("Enregistrer"))
            ->getHelper()->setAttribute('onclick', '$(\'#action\').val(\'save\');')->getElement()
        );
        
        $initialProducts = PM::getAutocompleteItems();
        $jsInit = '$(document).ready(function(){updateInvoiceForm(false,' . $initialProducts . ');});';
        Component::getJquery()
            ->registerScript(file_get_contents(__DIR__ . '/FormInvoice.min.js'))
            ->registerScript($jsInit);
    }
    
    /**
     * Type de document (facture, commande, devis), à spécifier AVANT l'initialisation
     * @param $type string|null
     * @return $this
     */
    protected function setType(string $type)
    {
        if (!in_array($type, InvoiceBean::TYPES)) {
            throw new ArchException('Badd invoice type [' . $type . ']');
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @return $this
     */
    public function setPreviewCodeNumber($previewCodeNumber)
    {
        $this->previewCodeNumber = $previewCodeNumber;
        return $this;
    }

    public function getPreviewCodeNumber()
    {
        return $this->previewCodeNumber;
    }
    
    /**
     * Produits récupérés depuis le formulaire
     * @return array
     */
    public function getProducts()
    {
        return $this->products;
    }
    
    /**
     * Produits provenant de la base de données, issu de la validation
     * @return array
     */
    public function getDbProducts()
    {
        return $this->dbProducts;
    }
    
    /**
     * @param InvoiceBean $bean
     * @return $this
     */
    public function setBean(InvoiceBean $bean, bool $isCopy = false, string $targetType = null, ?int $idDocumentLinked = null)
    {
        // Enregistrement du bean
        $this->bean = $bean;
        
        // Valeurs unitaires
        $values = [
            'recipient'     => $bean->getRecipient()->getIdCompany(),
            'code'          => $isCopy ? null : $bean->getCode(),
            'date_sending'  => $isCopy ? null : $bean->getDateSending()->format('d/m/Y'),
            'date_validity' => $isCopy ? null : ($bean->getDateValidity() ? $bean->getDateValidity()->format('d/m/Y') : null),
            'md_before'     => $bean->getMdBefore(),
            'md_after'      => $bean->getMdAfter(),
            'id_document_linked' => $idDocumentLinked ?? $bean->getIdDocumentLinked(),
            'no_tax_article'     => $bean->getNoTaxArticle(),
            'display_tax'        => $bean->getDisplayTax()
        ];
        
        // S'il s'agit d'une facture, est-ce un avoir ?
        if ($targetType === InvoiceBean::TYPE_INVOICE) {
            $values['credit'] = $bean->getCredit();
        }
        
        // Le destinataire a-t-il été supprimé ?
        if (!$bean->getRecipient()->getIdCompany() || !DB::getCompanyTable()->find($bean->getRecipient()->getIdCompany())) {
            $msg = __("Attention, le destinataire de ce document est inexistant ou a été supprimé de votre liste de contacts.");
            C::getJsonRequest()->addAlert(__("Destinataire inexistant"), $msg, 'warning');
        }

//        // Lien vers le document original s'il s'agit d'une transformation 
//        // devis -> commande ou commande -> facture ou devis -> facture
        $beanLibs = $bean->getLibs();
//        $newObj = false;
//        if (!$bean->isCredit() && (($targetType === InvoiceBean::TYPE_INVOICE && $bean->getType() === InvoiceBean::TYPE_ORDER)
//        ||  ($targetType === InvoiceBean::TYPE_ORDER   && $bean->getType() === InvoiceBean::TYPE_QUOTE)
//        ||  ($targetType === InvoiceBean::TYPE_INVOICE && $bean->getType() === InvoiceBean::TYPE_QUOTE))) {
//            $beanLibs['Objet :'] = sprintf(__("%s basée sur %s %s"), 
//                InvoiceBean::getTypeNameFromType($targetType, true), 
//                $bean->getTypeName(false, InvoiceBean::getPrefixesSingular()), 
//                $bean->getCode());
//            $newObj = true;
//        }
//        
//        // Si c'est un nouvel avoir... 
//        if ($bean->isCredit() && $bean->isInvoice() && $targetType === InvoiceBean::TYPE_INVOICE && $idDocumentLinked) {
//            $linkedBean = DB::getInvoiceTable()->getInvoiceBeanFromIdDocument($idDocumentLinked);
//            $beanLibs['Objet :'] = sprintf(__("Facture d'avoir en remboursement de la facture %s"), $linkedBean->getCode());
//            $newObj = true;
//        }
//        $newObj && C::getJsonRequest()->addAlert(__("Objet mis à jour"), sprintf(__("Nouvel objet : '%s'."), $beanLibs['Objet :']), 'info');
        
        // Libellés
        $cpt = 1;
        foreach ($beanLibs as $key => $value) {
            $values['k_libs_' . $cpt] = $key;
            $values['v_libs_' . $cpt] = $value;
            $cpt++;
        }
        

        // Produits
        /* @var $product \Osf\Pdf\Document\Bean\ProductBean */
        $cpt = 0;
        foreach ($bean->getProducts() as $product) {
            $values['pd' . $cpt] = $product->getId();
            $values['pq' . $cpt] = $product->getQuantity();
            if ($product->getComment()) {
                $values['pdd' . $cpt] = $product->getComment();
            }
            // $values['ph'  . $cpt] = '';
            if (!$product->getPriceIsDefault()) {
                $values['pp' . $cpt] = $product->getPriceType() == ProductBean::PRICE_HT ? $product->getPriceHT() : $product->getPriceTTC();
            }
            if (!$product->getDiscountIsDefault()) {
                $values['pr' . $cpt] = $product->getDiscount();
            }
            $cpt++;
        }
        
        // Enregistrement des valeurs pour affichage du formulaire
        $this->registerValues($values);
        
        // Fin de populate via isValid() (container, etc.)
        $this->hydrate($values, null, true, true);
        
        return $this->isValid();
    }
    
    /**
     * Appel isPosted() parent et enregistre les valeurs pour affichage du formulaire
     * @return boolean
     */
    public function isPosted(bool $registerValues = true)
    {
        // Si pas de post, on retourne false
        $posted = parent::isPosted();
        if (!$posted) {
            return false;
        }
        
        // Sinon on enregistre les valeurs
        if ($registerValues) {
            $this->registerValues($this->getPostedValues());
        }
        
        // Et on retourne le résultat de posted (true)
        return $posted;
    }
    
    /**
     * Filtre et vérifie les valeurs du formulaire, crée les produits si nécessaire, 
     * puis construit les données JSON d'hydratation du formulaire
     * @param array $values
     * @return $this
     */
    protected function registerValues(array $values)
    {
        // Prévient isValid() s'il y a des erreurs
        $this->hasErrors = false;
        
        // Ajout des nouvelles options de libellés
        // @task [INVOICE] Déplacer là ou les valeurs sont populées
        if (isset($values['k_libs_1']) && $values['k_libs_1'] && !array_key_exists($values['k_libs_1'], $this->getElement('k_libs_1')->getOptions())) {
            $this->getElement('k_libs_1')->addOption($values['k_libs_1'], $values['k_libs_1']);
        }
        if (isset($values['k_libs_2']) && $values['k_libs_2'] && !array_key_exists($values['k_libs_2'], $this->getElement('k_libs_2')->getOptions())) {
            $this->getElement('k_libs_2')->addOption($values['k_libs_2'], $values['k_libs_2']);
        }
        if (isset($values['k_libs_3']) && $values['k_libs_3'] && !array_key_exists($values['k_libs_3'], $this->getElement('k_libs_3')->getOptions())) {
            $this->getElement('k_libs_3')->addOption($values['k_libs_3'], $values['k_libs_3']);
        }
        
        // Récupération et réorganisation des produits
        $products = [];
        $blackList = [];
        $ids = [];
        $addList = [];
        foreach ($values as $key => $value) {
            
            // Filtrage des données à récupérer
            if (!preg_match('/^(pd|pdd|pq|pp|pr|ph)\d+$/', $key) || $value === '') {
                continue;
            }
            $pid = filter_var($key, FILTER_SANITIZE_NUMBER_INT);
            $pkey = rtrim($key, '1234567890');
            
            // Suppression des produits vides et supprimés
            if (isset($blackList[$pid]) || ($pkey == 'pd' && !$value) || ($pkey == 'ph' && $value == -1)) {
                $blackList[$pid] = true;
                if (isset($products[$pid])) {
                    unset($products[$pid]);
                }
                continue;
            }
            
            // Ajout des ids dans les listes de produits à requêter ou à créer
            if ($pkey === 'pd') {
                if (is_numeric($value) && $value > 0) {
                    $ids[(int) $value][] = $pid;
                } else {
                    $addList[] = $pid;
                }
            }
            $products[$pid][$pkey] = $value;
        }
        
        // Filtrage et validation des produits
        $vCurrency = V::getCurrency();
        $fCurrenty = F::getCurrency();
        foreach ($products as &$product) {
            
            // Produit
            if (!isset($product['pd'])) {
                $product['errors']['pd'][] = __("Choisissez un produit");
            }
            
            // Quantité
            if (!isset($product['pq'])) {
                $product['errors']['pd'][] = __("La quantité est requise");
            } else if (!is_numeric($product['pq'])) {
                $product['errors']['pd'][] = __("Quantité incorrecte");
            } else if ($product['pq'] < 1) {
                $product['errors']['pd'][] = __("La quantité ne peut être négative ou nulle");
            } else if ($product['pq'] > 9999999) {
                $product['errors']['pd'][] = __("Cette quantité est excessive");
            }
            
            // Prix
            if (isset($product['pp'])) {
                $product['pp'] = $fCurrenty->filter($product['pp']);
            }
            if (isset($product['pp']) && (!is_numeric($product['pp']) || $product['pp'] < 0)) {
                $product['errors']['pp'][] = __("Le prix doit être une valeur positive.");
            } else if (isset($product['pp']) && !$vCurrency->isValid($product['pp'])) {
                $product['errors']['pp'] = array_values($vCurrency->getMessages());
            } else if (isset($product['pp']) && $product['pp'] > 9999999) {
                $product['errors']['pp'][] = sprintf(__("%s ne supporte pas les tarifs aussi élevés..."), APP_NAME);
            }
            
            // Remise
            if (isset($product['pr']) && (!is_numeric($product['pr']) || $product['pr'] < 0 || $product['pr'] > 100)) {
                $product['errors']['pr'][] = __("Remise incorrecte");
            }
            
            // Description (commentaire complémentaire)
            if (isset($product['pdd']) && T::strLen($product['pdd']) > 1000) {
                $product['errors']['pdd'][] = vsprintf(__("Veuillez réduire ce texte (%d signes sur %d maxi)"), [T::strLen($product['pdd']), 1000]);
            }
            if (isset($product['pd']) && $product['pd'] == -1) {
                if (!isset($product['pp'])) {
                    $product['errors']['pp'][] = __("Prix requis pour l'ajout du produit.");
                    $product['pp'] = '';
                }
                $product['option'] = [
                    'id' => '-1',
                    'uid' => '-1',
                    'title' => $product['ph'],
                    'price' => $product['pp'],
                    'code' => '[NEW]',
                    'price_type' => I::hasTax() ? 'ht' : 'ttc'
                ];
            }
            if (isset($product['errors'])) {
                $this->hasErrors = true;
            }
        }
        
        // Ajout des nouveaux produits en base
        if (!$this->hasErrors && $addList) {
            foreach ($addList as $pid) {
                if (!isset($products[$pid]) || isset($products[$pid]['errors'])) {
                    continue;
                }
                $newRow = [
                    'title' => $products[$pid]['ph'],
                    'price' => $products[$pid]['pp'],
                    'code' => FormProduct::nextProductCode($products[$pid]['ph']),
                    'description' => '',
                    'status' => 1
                ];
                $id = PM::addProduct($newRow);
                if ($id) {
                    $ids[$id][] = $pid;
                    $products[$pid]['pd'] = $id;
                    unset($products[$pid]['option']);
                    $products[$pid]['info'] = __("Produit ajouté.");
                } else {
                    $products[$pid]['errors']['pd'][] = __("Impossible d'insérer ce produit. Tentez de le modifier.");
                }
            }
        }
        
        // Ajout des informations produits depuis la base
        if ($ids) {
            $fields = ['id', 'uid', 'code', 'title', 'price', 'price_type'];
            $result = DB::getProductTable()->findIds(array_keys($ids));
            foreach ($result as $row) {
                foreach ($ids[$row['id']] as $pid) {
                    $products[$pid]['option'] = Tab::reduce($row, $fields);
                }
                $this->dbProducts[$row['id']] = $row;
                unset($ids[$row['id']]);
            }
            foreach ($products as $key => $value) {
                if (!isset($value['pd']) || !$value['pd'] || !isset($value['option'])) {
                    unset($products[$key]);
                }
            }
            $delCount = count($ids);
            if ($delCount) {
                $msg = __("%d produit(s) indisponible(s). Ce ou ces produits ont été supprimés de votre catalogue, nous ne pouvons pas les reporter dans ce document.");
                C::getJsonRequest()->addAlert(__("Produit(s) supprimé(s)"), sprintf($msg, $delCount), 'warning');
            }
            $this->products = $products;
        }
        
        // Encodage JSON
        $jsonValues = Json::encode(array_values($products));
        $jsonScript = '$(document).ready(function(){hydrateProducts(' . $jsonValues . ');});';
        Component::getJquery()->registerScript($jsonScript);
        
        return $this;
    }
    
    public function isValid($values = null)
    {
        $valid = parent::isValid($values);
        
        // Dates
        if ($this->getElement('date_validity')->getValue() 
        && (DT::buildDate($this->getElement('date_validity')->getValue())->getTimestamp() < DT::buildDate($this->getElement('date_sending')->getValue())->getTimestamp())) {
            $this->getElement('date_validity')->addError(__("Cette date ne peut être inférieure à la date d'émission (envoi / facturation)"));
            $valid = false;
        }
        
        // Code qui existe déjà 
        $code = $this->getElement('code')->getValue();
        if ($code && DB::getInvoiceTable()->invoiceExists($code, $this->getDocumentId())) {
            $errMsg = __("Ce code existe déjà. Veuillez le modifier.");
            if ($this->getType() === InvoiceBean::TYPE_INVOICE) {
                $errMsg .= ' ' . __("Il est important que les numéros de facture se suivent sans interruption.");
            }
            $this->getElement('code')->addError($errMsg);
            $valid = false;
        }
        
        // Code généré si pas spécifié
        if (!$code) {
            $code = InvoiceBean::buildCode($this->getPreviewCodeNumber(), $this->getType());
            if (DB::getInvoiceTable()->invoiceExists($code, $this->getDocumentId())) {
                $msg = sprintf(__("Un document porte déjà le prochain code généré '%s'. Spécifiez ce code à la main ou ajustez les séquences dans [options] -> [mon compte]."), $code);
                $this->getElement('code')->addError($msg);
                $valid = false;
            }
        }
        
        // Pas d'article pour un client à facturer HT
        if (I::hasTax() && !$this->getElement('no_tax_article')->getValue()) {
            
            // Si c'est le client qui doit être facturé HT par défaut et qu'on ne force pas la facturation HT
            if ($this->getElement('recipient')->getValue() 
            &&  $this->getElement('display_tax')->getValue() === InvoiceBean::DISPLAY_TAX_AUTO) {
                $recipientBean = DB::getCompanyTable()->getContactBean($this->getElement('recipient')->getValue());
                if (!$recipientBean->getChargeWithTax()) {
                    $this->getElement('no_tax_article')->addError(__("Facture HT : vous devez spécifier la mention légale justificative pour ce client. Si vous ne devez pas facturer HT, forcez la facturation avec TVA grâce au champ 'Facture HT ou TTC ?' ci-dessus."));
                    $valid = false;
                }
            }
            
            // Si on force la facturation HT
            else if ($this->getElement('display_tax')->getValue() === InvoiceBean::DISPLAY_TAX_NO) {
                $this->getElement('no_tax_article')->addError(__("Facture HT : vous devez spécifier la mention légale justificative."));
                $valid = false;
            }
        }
        
        // TVA due par le preneur, article 262 ter, I du CGI
        // Lien sur lui-même (à voir...)
        
        if (!$valid || $this->hasErrors) {
            return false;
        }
        return $valid;
    }
    
    /**
     * @param int $documentId
     * @return $this
     */
    public function setDocumentId(int $documentId)
    {
        $this->documentId = $documentId;
        return $this;
    }

    /**
     * @return int
     */
    public function getDocumentId(): int
    {
        return (int) $this->documentId;
    }
}
