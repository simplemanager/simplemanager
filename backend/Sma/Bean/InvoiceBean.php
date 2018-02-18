<?php
namespace Sma\Bean;

use Osf\View\Helper\Bootstrap\AbstractViewHelper as AVH;
use Osf\Pdf\Document\Bean\InvoiceBean as IB;
use Osf\Exception\ArchException;
use Osf\Stream\Text;
use Sma\Session\Identity as I;
use Sma\Container as C;
use DateTime;
use DB;

/**
 * Tout document de type facture, commande, devis
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage bean
 */
class InvoiceBean extends IB implements DocumentBeanInterface, Addon\WarningInterface
{
    use Addon\Template;
    use Addon\WarningTools;
    
    protected $idDocument;
    protected $idDocumentLinked;
    protected $idDocumentHistory;
    protected $idInvoice;
    
    public function __construct(string $type = self::TYPE_INVOICE)
    {
        parent::__construct($type);
        $this->setConfidential(true);
        $this->setDefaults();
    }
    
    /**
     * Définit des valeurs par défaut en fonction de l'environnement et du profil utilisateur
     * @return $this
     */
    public function setDefaults()
    {
        $this->setTaxFranchise(I::getParam('company', 'taxfranch'));
        $this->setQrCode(I::getParam('invoice', 'qrcode'));
        $this->setDisplayCreatedBy(I::getParam('document', 'madesma'));
        $this->buildDefaultPenaltyMention();
        return $this;
    }
    
    public function getSearchData(): string
    {
        $data = [
            $this->getCode(),
            $this->getTitle(),
            $this->getDateSending()->format('d/m/Y'),
            ($this->getDateValidity() ? $this->getDateValidity()->format('d/m/Y') : ''),
            $this->getDescription(),
            $this->getMdBefore(),
            $this->getMdAfter(),
            $this->getRecipient()->getComputedTitle()
        ];
        $pattern = '/^([a-zA-Z]+)0+([1-9][0-9]*)$/';
        if (preg_match($pattern, $this->getCode())) {
            $data[] = preg_replace($pattern, '$1$2_', $this->getCode());
        }
        foreach ($this->getLibs() as $lib) {
            $data[] = $lib;
        }
        /* @var $product \Osf\Pdf\Document\Bean\ProductBean */
        foreach ($this->getProducts() as $product) {
            $data[] = $product->getCode() . ' ' 
                    . $product->getTitle() . ' ' 
                    . $product->getDescription();
        }
        return implode(' ', array_filter($data));
    }
    
    /**
     * Url vers le document
     * @return string
     */
    public function buildUrl(): string
    {
        if (!$this->getId()) {
            throw new ArchException('Id is required to build url');
        }
        $params = ['type' => $this->getType(), 'id' => $this->getId()];
        return C::getRouter()->buildUri($params, 'invoice', 'view');
    }
    
    /**
     * Construit un nom de fichier pour ce document
     * @param int $version version à ajouter
     * @param DateTime $date date de la révision à substituer à la date du bean
     * @return string
     */
    public function buildFileName(int $version = null, DateTime $date = null): string
    {
        return $this->filenameDate($date) . '_' . Text::getAlpha($this->getTypeName(true)) . '_' 
                . trim(Text::getAlpha($this->getCode()), '-') . '_'
                . trim(Text::getAlpha($this->getRecipient()->getComputedTitle()), '-')
                . ($version !== null ? '-v'  . $version : '')
                . '.pdf';
    }
    
    /**
     * Construit un code à partir des paramètres
     * @param string $codeNo
     * @param string $type
     * @return string
     */
    public static function buildCode($codeNo, string $type): string
    {
        $codeFormat = I::getParam('invoice', 'code_' . $type);
        
        // Code par défaut si null
        if (!$codeFormat) {
            return parent::buildCode($codeNo, $type);
        }
        
        // Création du code
        $tags = [];
        preg_match_all('/\[([^]]+)\]/', $codeFormat, $tags);
        $replaces = [];
        $params = [];
        $dateFormats = ['yy' => 'y', 'yyyy' => 'Y', 'mm' => 'm'];
        foreach ($tags[1] as $tag) {
            switch ($tag) {
                case 'nnn' : 
                case 'nnnn' : 
                case 'nnnnn' : 
                case 'nnnnnn' : 
                    $replaces['[' . $tag . ']'] = "%'0" . strlen($tag) . 'd';
                    $params[] = $codeNo;
                    break;
                case 'yy' : 
                case 'yyyy' : 
                case 'mm' : 
                    $replaces['[' . $tag . ']'] = date($dateFormats[$tag]);
                    break;
                default : 
                    throw new ArchException('Tag [' . $tag . '] non géré pour générer un code');
            }
        }
        return vsprintf(str_replace(array_keys($replaces), array_values($replaces), $codeFormat), $params);
    }
    
    /**
     * Crée la mention des pénalités à partir des paramètres par défaut
     * @throws ArchException
     * @return $this
     */
    protected function buildDefaultPenaltyMention()
    {
        $phrase = I::getParam('invoice', 'penal_phrase') ?? __("Taux de pénalités exigibles de plein droit et sans rappel préalable en cas de paiement à une date ultérieure à celle figurant sur la facture : {{rate}}. Une indemnité forfaitaire fixe de 40 € pour frais de recouvrement sera appliquée en cas de paiement à une date ultérieure à celle figurant sur la facture. Si les frais de recouvrement sont supérieurs à ce montant, une indemnisation complémentaire sera due sur présentation des justificatifs.");
        $type   = I::getParam('invoice', 'penal') ?? 'bce10';
        return $this->setPenalty($type, $phrase);
    }
    
    /**
     * Définit les pénalités
     * @param string $type
     * @param string $phrase
     * @return $this
     * @throws ArchException
     */
    public function setPenalty(string $type, string $phrase)
    {
        switch (true) {
            case $type === 'bce10' : 
                $rate = __("taux légal de la BCE majoré de 10 points");
                break;
            case $type === '3bce' : 
                $rate = __("3 fois le taux d'intérêt légal");
                break;
            case is_numeric($type) && $type >= 5 && $type <= 15 : 
                $rate = $type . '%';
                break;
            default : 
                throw new ArchException('Type de pénalité [' . $type . '] non reconnu');
        }
        $this->setPenaltyMention(str_replace('[rate]', $rate, $phrase));
        return $this;
    }
    
    /**
     * @param $idDocument int|null
     * @return $this
     */
    public function setIdDocument($idDocument)
    {
        $this->idDocument = Text::numericOrNull($idDocument);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdDocument()
    {
        return $this->idDocument;
    }
    
    /**
     * @param $idDocumentHistory int|null
     * @return $this
     */
    public function setIdDocumentHistory($idDocumentHistory)
    {
        $this->idDocumentHistory = Text::numericOrNull($idDocumentHistory);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdDocumentHistory()
    {
        return $this->idDocumentHistory;
    }
    
    /**
     * @param $idInvoice int|null
     * @return $this
     */
    public function setIdInvoice($idInvoice)
    {
        $this->idInvoice = Text::numericOrNull($idInvoice);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdInvoice(): ?int
    {
        return $this->idInvoice;
    }
    
    /**
     * @param int|null $idDocumentLinked
     * @return $this
     */
    public function setIdDocumentLinked($idDocumentLinked, bool $checkAndWriteLinkedDocument = true)
    {
        if ($idDocumentLinked && $checkAndWriteLinkedDocument) {
            $bean = DB::getInvoiceTable()->getInvoiceBeanFromIdDocument($idDocumentLinked);
            $this->setLinkedDocumentCode($bean->getCode());
            $this->setLinkedDocumentType($bean->getType());
        }
        $this->idDocumentLinked = Text::numericOrNull($idDocumentLinked);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getIdDocumentLinked(): ?int
    {
        return $this->idDocumentLinked;
    }
    
    // Id officiel = document id (compatibilité avec DocumentBeanInterface)
    
    /**
     * @param $id int|null
     * @return $this
     */
    public function setId($id)
    {
        return $this->setIdDocument($id);
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->getIdDocument();
    }
    
    /**
     * Retourne un warning si besoin (retards, manques, etc.), par ordre d'importance
     * @param bool $firstWarnOnly
     * @param bool $disallowSendWarnings uniquement les alertes qui empêchent un envoi
     * @param bool $withAdvices inclus des conseils non critiques
     * @param bool $html retourner la version HTML
     * @return array|null Texte du warning
     */
    public function getWarnings(bool $firstWarnOnly = false, bool $disallowSendWarnings = false, bool $withAdvices = false, bool $html = false): ?array
    {
        $warns = [];
        
        // Pas de destinataire
        if (!$this->getRecipient()->getId() && !$this->getRecipient()->getIdCompany()) {
            $warns[] = $this->newWarn(__("Aucun destinataire. Editez votre document."), 'warning', AVH::STATUS_ERROR, 
                        $html, 'invoice', 'edit', ['type' => $this->getType(), 'id' => $this->getIdDocument()]);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Le destinataire existe mais...
        else {
            
            // Il n'a pas d'e-mail
            if (!$this->getRecipient()->getEmail()) {
                $warns[] = $this->newWarn(__("Destinataire sans e-mail, envoi impossible"), 'warning', null, 
                        $html, 'recipient', 'edit', ['id' => $this->getRecipient()->getIdCompany(), 'f' => 'email']);
                if ($firstWarnOnly) { return $warns[0]; }
            }
        
            // Son adresse est incomplète
            if (!$this->getRecipient()->getAddress()->isFull()) {
                $warns[] = $this->newWarn(
                            __("L'adresse du destinataire est incomplète (information obligatoire)"), 'warning', null, 
                        $html, 'recipient', 'edit', ['id' => $this->getRecipient()->getIdCompany(), 'f' => 'a~' . $this->getRecipient()->getAddress()->getNotFullField()]);
                if ($firstWarnOnly) { return $warns[0]; }
            }
        }
        
        // Facture > 60 jours de délai
        if (($this->getType() === IB::TYPE_INVOICE)
         && ($this->getDateValidity()->getTimestamp() - $this->getDateSending()->getTimestamp() >= (61 * 24 * 3600))) {
            $warns[] = $this->newWarn(__("Ne dépassez pas 60 jours entre date d'émission et de paiement"), 'warning', AVH::STATUS_ERROR, 
                        $html, 'invoice', 'edit', ['type' => $this->getType(), 'id' => $this->getIdDocument()]);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Expéditeur incomplet
        if (!$this->getProvider()->getAddress()->isFull()) {
            $warns[] = $this->newWarn(__("Votre adresse est incomplète"), 'warning', AVH::STATUS_ERROR, 
                       $html, 'account', 'company', ['edit' => 'company']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Statut + Capital ou N° enregistrement + préfecture
        if (!$this->getTaxFranchise() && !$this->getProvider()->getCompanyIntro()) {
            $warns[] = $this->newWarn(__("Votre statut & capital ou n° + préfecture (asso) sont obligatoires (préférences)"), 'warning', null, 
                       $html, 'account', 'features', ['f' => 'company~creation']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // RCS pour entreprises autres que association
        if (!in_array(I::getParam('company', 'legal_status'), ['a', 'ei']) && !$this->getTaxFranchise() && !$this->getProvider()->getCompanyRegistration()) {
            $warns[] = $this->newWarn(__("Votre RCS ou tribunal de commerce est obligatoire (préférences)"), 'warning', null, 
                       $html, 'account', 'features', ['f' => 'company~creation']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
            
        
        // SIREN
        if (!$this->getTaxFranchise() && !$this->getProvider()->getCompanySiret()) {
            $warns[] = $this->newWarn(__("Votre SIRET/SIREN est obligatoire (préférences)"), 'warning', null, 
                       $html, 'account', 'features', ['f' => 'company~siret']);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Facture pas payée ou commande pas signée
        if (!$disallowSendWarnings 
         && ($this->getStatus() === IB::STATUS_SENT)
         && ($this->getType() === IB::TYPE_INVOICE || $this->getType() === IB::TYPE_ORDER)
         && ($this->getDateValidity()->getTimestamp() < time())) {
            $warns[] = $this->getType() === IB::TYPE_INVOICE 
                    ? $this->newWarn(__("Retard de paiement"), 'clock-o', AVH::STATUS_ERROR)
                    : $this->newWarn(__("Dépassement de délai"), 'clock-o', AVH::STATUS_ERROR);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        // Envoi en retard
        if (!$disallowSendWarnings 
         && ($this->getStatus() === IB::STATUS_CREATED) 
         && ($this->getDateSending()->getTimestamp() < time() - (2 * 24 * 3600))) {
            $warns[] = $this->newWarn(__("Envoyez ce document sans tarder"), 'clock-o', null, 
                    $html, 'invoice', 'send', ['id' => $this->getIdDocument()]);
            if ($firstWarnOnly) { return $warns[0]; }
        }
        
        return $warns;
    }
    
    /**
     * Met à jour le bean avec les informations contenues dans le row
     * @param array $row
     * @return $this
     */
    public function update(array $row)
    {
        $this->setStatus($row['status'])
            ->setType($row['type'])
            ->setCode($row['code'])
            ->setDescription($row['description'])
            ->setIdInvoice($row['id']);
        return $this;
    }
}
