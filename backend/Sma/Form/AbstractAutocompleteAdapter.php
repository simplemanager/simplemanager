<?php
namespace Sma\Form;

use Osf\Form\Element\ElementSelect\AutocompleteAdapterInterface;
use Osf\Form\Element\ElementSelect;
use Osf\Stream\Json;
use Sma\Container as C;

/**
 * Gestion des autocomplétions
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package osf
 * @subpackage form
 */
abstract class AbstractAutocompleteAdapter implements AutocompleteAdapterInterface
{
    
    /**
     * Ajoute les options correspondant aux valeurs courantes du champ
     * @param ElementSelect $elt
     * @param string $category
     * @return array
     */
    protected static function registerAutocompleteOptions(ElementSelect $elt, string $category): array
    {
        $initialItems = [];
        if ($elt->getValue()) {
            $values = is_array($elt->getValue()) ? $elt->getValue() : [$elt->getValue()];
            foreach ($values as $value) {
                $searchItems = C::getSearch()->searchAutocomplete('', $category . (int) $value);
                $items = Json::decode($searchItems);
                $initialItems = array_merge($initialItems, $items);
                if (isset($items[0])) {
                    $elt->addOption($items[0]->id, $items[0]->title);
                }
            }
        }
        return $initialItems;
    }
    
    /**
     * Ajout des options chargées au départ via le moteur de recherche
     * @param int $limit
     * @param array $items
     * @param string $category
     * @return string
     */
    protected static function appendInitialOptions(int $limit, array $items, string $category): string
    {
        // S'il n'y a pas d'items à aller chercher, on sérialise les items par défaut
        if ($limit <= 0) {
            return json_encode($items);
        }
        
        // On crée un tableau d'ids
        $ids = [];
        foreach ($items as $key => $item) {
            $ids[$item->id] = $key;
        }
        
        // Items récupérés via le moteur de recherche...
        $initialItems = C::getSearch()->searchAutocomplete('', $category, $limit);

        // S'il y a au moins un item par défaut, il va falloir merger...
        if (isset($items[0])) {
            
            // On récupère les nouveaux items, on élague la liste des items par défaut
            $newItems = Json::decode($initialItems);
            foreach ($newItems as $item) {
                if (isset($ids[$item->id])) {
                    unset($items[$ids[$item->id]]);
                }
            }
            
            // S'il reste des items, on merge
            if ($items) {
                $items = array_merge($items, $newItems);
                $initialItems = Json::encode($items);
            }
        }

        // On retourne les items sérialisés
        return $initialItems;
    }
}