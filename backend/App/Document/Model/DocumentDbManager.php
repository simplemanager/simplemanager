<?php
namespace App\Document\Model;

use Osf\Exception\ArchException;
use Osf\Form\Element\ElementSelect;
use Sma\Form\AbstractAutocompleteAdapter as AAA;
use Sma\Db\DocumentTable as DocTable;
use Sma\Bean\InvoiceBean as IB;
use Sma\Bean\LetterBean as LB;
use Sma\Container as C;

/**
 * Recipient db management
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage recipient
 */
class DocumentDbManager extends AAA
{
    const CATEGORY = DocTable::SEARCH_CATEGORY; // Pour les items de recherche / autocomplétion
    
    const TYPE_INVOICES = IB::TYPE_ALL;
    const TYPE_INVOICE  = IB::TYPE_INVOICE;
    const TYPE_ORDER    = IB::TYPE_ORDER;
    const TYPE_QUOTE    = IB::TYPE_QUOTE;
    const TYPE_LETTER   = LB::TYPE;
    
    const TYPES = [
        self::TYPE_INVOICES,
        self::TYPE_INVOICE,
        self::TYPE_ORDER,
        self::TYPE_QUOTE,
        self::TYPE_LETTER,
    ];
    
    protected $type;
    protected $filters;
    protected $autocompleteLimit = 10;
    
    /**
     * @param string|null $documentType
     * @param array $dataTypeFilters
     * @param int|null $autocompleteLimit
     * @throws ArchException
     */
    public function __construct(?string $documentType = null, ?array $dataTypeFilters = null, ?int $autocompleteLimit = null)
    {
        if (!is_null($documentType) && !in_array($documentType, self::TYPES)) {
            throw new ArchException('Bad document type [' . $documentType . ']');
        }
        if ($autocompleteLimit !== null) {
            $this->autocompleteLimit = $autocompleteLimit;
        }
        $this->type = $documentType ?: self::CATEGORY;
        $this->filters = $dataTypeFilters;
    }
    
    /**
     * Attache à l'élément select une autocomplétion sur les documents
     * @param ElementSelect $elt
     * @param int $limit Nombre d'éléments initiaux
     * @return ElementSelect
     */
    public function registerAutocomplete(ElementSelect $elt = null, ?int $limit = null): ElementSelect
    {
        // Récupération des données liées à la valeur de l'élément et création de l'option
        $items = $this->registerAutocompleteOptions($elt, $this->type);
        
        // Liste d'items proposé par défaut, auquel on ajout les items courants s'ils 
        // existent et ne sont pas dans la liste des items proposés (à optimiser)
        $initialItems = $this->appendInitialOptions($limit ?? $this->autocompleteLimit, $items, $this->type);
        
        // Enregistrement de l'autocomplétion
        $template = "'<div>' + '<strong>' + escape(item.title) + '</strong> '"
                //. " + '<span class=\"pull-right\">' + escape(item.total_ht) + '</span>'"
                . " + '</div>'";
        $url = C::getViewHelper()->url('event', 'ac') . '/' . $this->type . '/';
        $elt = $elt ?: new ElementSelect($this->type);
        $elt->setAutocomplete($url, $template, $initialItems);
        
        // Placeholder en fonction du contexte
        if (!$elt->getPlaceholder()) {
            if ($elt->isMultiple()) {
                $elt->setPlaceholder(__("Choisir un ou plusieurs documents"));
            } else {
                $elt->setPlaceholder(__("Choisir un document"));
            }
        }
        
        return $elt;
    }
}
