<?php
namespace App\Admin\Model;

use Osf\Helper\Mysql;
use Sma\Container as C;
use Osf\Form\Element\ElementSelect;
use Sma\Form\AbstractAutocompleteAdapter as AAA;
use DB;

/**
 * Account db management
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage admin
 */
class AccountDbManager extends AAA
{
    const CATEGORY = 'account'; // Pour les items de recherche / autocomplétion
    const SEARCH_FIELDS = ['id', 'concat(lastname, concat(\' \', firstname)) as title', 'email']; //, "'' as search_content"];
    
    protected $autocompleteLimit = null;
    
    public function __construct(?int $autocompleteLimit = null)
    {
        if ($autocompleteLimit !== null) {
            $this->autocompleteLimit = $autocompleteLimit;
        }
    }
    
    /**
     * @param string $phrase
     * @return string
     */
    public static function searchForAutocomplete(string $phrase, ?int $limit = null): string
    {
        $sql = 'SELECT ' . implode(', ', self::SEARCH_FIELDS) . ' '
             . 'FROM account '
             . 'WHERE status=\'enabled\' AND (id=? OR firstname LIKE ? OR lastname LIKE ? OR email LIKE ?) '
             . 'ORDER BY ' . ($phrase === '' ? 'id DESC' : 'lastname, firstname') . ' '
             . 'LIMIT ' . ($limit ?? 20);
        $params[] = is_numeric($phrase) ? (int) $phrase : '';
        $params[] = Mysql::like($phrase);
        $params[] = $params[1];
        $params[] = $params[1];
        return self::buildJsonContent($sql, $params);
    }
    
    /**
     * @param array $ids
     * @return string
     */
    public static function searchIdsForAutocomplete(array $ids): string
    {
        $sql = 'SELECT ' . implode(', ', self::SEARCH_FIELDS) . ' '
             . 'FROM account '
             . 'WHERE id IN (' . implode(',', $ids) . ')';
        return self::buildJsonContent($sql);
    }
    
    /**
     * @param string $sql
     * @param array $params
     * @return string
     */
    protected static function buildJsonContent(string $sql, ?array $params = null): string
    {
        $results = DB::getAccountTable()->prepare($sql)->execute($params);
        $jsonArray = [];
        foreach ($results as $row) {
            $row['search_content'] = implode(' ', $row);
            $jsonArray[] = $row;
        }
        return json_encode($jsonArray);
    }
    
    /**
     * Attache à l'élément select une autocomplétion sur les contacts
     * @param ElementSelect $elt
     * @param int $limit Nombre d'éléments initiaux
     * @return ElementSelect
     */
    public function registerAutocomplete(ElementSelect $elt = null, ?int $limit = null): ElementSelect
    {
        // Derniers comptes créés par défaut
        $limit = $limit ?? $this->autocompleteLimit;
        $initialItems = $elt->getValue() ? self::searchIdsForAutocomplete($elt->getValue()) : ($limit ? self::searchForAutocomplete('', $limit) : null);
        
        // Enregistrement de l'autocomplétion
        $template = "'<div>' + '<strong>#' + escape(item.id) + '</strong> '"
                . " + escape(item.title)"
                . " + '<span class=\"pull-right\">' + escape(item.email) + '</span>'"
                . " + '</div>'";
        $url = C::getViewHelper()->url('admin', 'ac') . '/' . self::CATEGORY . '/';
        $elt = $elt ?: new ElementSelect(self::CATEGORY);
        $elt->setAutocomplete($url, $template, $initialItems);
        
        // Placeholder en fonction du contexte
        if (!$elt->getPlaceholder()) {
            if ($elt->isMultiple()) {
                $elt->setPlaceholder(__("Choisir un ou plusieurs comptes"));
            } else {
                $elt->setPlaceholder(__("Choisir un compte"));
            }
        }
        
        return $elt;
    }
}
