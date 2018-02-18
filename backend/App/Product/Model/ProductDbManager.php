<?php
namespace App\Product\Model;

use Osf\Helper\Tab;
use Osf\Helper\Mysql;
use Osf\Form\Element\ElementSelect;
use Osf\Form\Element\ElementSelect\AutocompleteAdapterInterface as AAI;
use Sma\Log;
use Sma\Session\Identity as I;
use Sma\Container as C;
use App\Product\Form\FormProduct;
use DB; //, H;

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
class ProductDbManager implements AAI
{
    const CATEGORY = 'product'; // Pour les items de recherche / autocomplétion
    const SEARCH_FIELDS = ['id', 'uid', 'code', 'title', 'price', 'price_type', 'tax', 'unit', 'discount'];
    
    protected $autocompleteLimit = 10;
    
    public function __construct(?int $autocompleteLimit = null)
    {
        if ($autocompleteLimit !== null) {
            $this->autocompleteLimit = $autocompleteLimit;
        }
    }
    
    /**
     * Ajout d'un produit
     * @param array $values
     * @return false|int clé primaire du nouveau produit
     */
    public static function addProduct(array $values)
    {
        Log::info("Ajout d'un produit", 'DB', $values);
        $values['id_account'] = I::getIdAccount();
        $values['price'] = Mysql::toDecimal($values['price']);
        $values['uid'] = DB::getSequenceTable()->nextValue('product');
        self::appendTaxValues($values);
        try {
            DB::getProductTable()->insert($values);
            $id = DB::getProductTable()->lastInsertValue;
            self::updateSearchIndex($id, $values);
            return $id;
        } catch (Exception $e) {
            Log::error('Product insert error: ' . $e->getMessage(), 'DB', $e);
        }
        return false;
    }
    
    /**
     * Mise à jour d'un produit
     * @param array $values
     * @param int $idProduct
     * @return boolean
     */
    public static function updateProduct(array $values, int $idProduct)
    {
        // Récupération d'informations
        Log::info("Mise à jour d'un produit", 'DB', $values);
        $idAccount = I::getIdAccount();
        $product = DB::getProductTable()->find($idProduct);
        
        // Est-ce un produit de l'utilisateur courant ?
        if (!$product || $idAccount !== $product->getIdAccount()) {
            Log::error("Tentative d'update interdite", 'DB', [$values, $idProduct, $product]);
            return false;
        }
        
        // Récupération de l'uid pour l'inclure dans les données de recherche
        $uid = $product->getUid();
        
        // Mise à jour
        try {
            $values['price'] = Mysql::toDecimal($values['price']);
            self::appendTaxValues($values);
            $nb = DB::getProductTable()->update($values, ['id' => $idProduct, 'id_account' => $idAccount]);
            if ($nb !== 1) {
                Log::error('Nombre de produits mis à jour : [' . $nb . '] au lieu de 1.', 'DB');
            }
            $values['uid'] = $uid;
            self::updateSearchIndex($idProduct, $values);
            return true;
        } catch (Exception $e) {
            Log::error('Product update error: ' . $e->getMessage(), 'DB', $e);
        }
        return false;
    }
    
    /**
     * Ajoute ttc/ht s'il y a une taxe
     * @param array $values
     */
    protected static function appendTaxValues(array &$values)
    {
        if (I::hasTax()) {
            $values['tax']        = isset($values['tax'])          ? $values['tax']      : FormProduct::getDefaultTax();
            $values['price_type'] = isset($values['price_type']) ? $values['price_type'] : FormProduct::getHtOrTtc();
        } else {
            $values['tax']        = isset($values['tax'])        ? $values['tax']        : 0;
            $values['price_type'] = isset($values['price_type']) ? $values['price_type'] : 'ttc';
        }
    }
    
    /**
     * @return \Zend\Db\Adapter\Driver\ResultInterface
     */
    public static function getProductsForTable(array $settings, array $columns = null)
    {
        $sorts = [
            'ca'  => 'product.code ASC',
            'cd'  => 'product.code DESC',
            'pa'  => 'product.price ASC',
            'pd'  => 'product.price DESC',
            'dca' => 'product.date_insert ASC',
            'dcd' => 'product.date_insert DESC',
            'dua' => 'product.date_update ASC',
            'dud' => 'product.date_update DESC',
        ];
        
        // var_dump($settings);
        
        $sortBy = isset($settings['s']) && isset($sorts[$settings['s']]) ? $sorts[$settings['s']] : 'product.id DESC';
//        $items = [
//            '<div><strong>|{IF(company.url > "",' . Mysql::concat('<a href="|{company.url}|" target="_blank">') . ',"")}|{company.title}|{IF(company.url > "","</a>","")}|</strong></div>|{IF(contact.firstname > "",' . Mysql::concat('<div>|{contact.firstname}| |{contact.lastname}|</div>') . ',"")}|',
//            '|{IF(company.email > "",' . Mysql::concat('<a href="mailto:|{company.email}|">|{company.email}|</a>') . ',"<span class=\"hidden-xs\">&nbsp;</span>")}|',
//            '|{IF(address.address > "",' . Mysql::concat('<span class="hidden-xs">|{replace(address.address,"\n","<br />")}|<br />|{address.postal_code}| |{address.city}|</span>') . ',"")}|',
//            '|{IF(company.tel > "",' . Mysql::concat('<a href="tel:|{company.tel}|">|{company.tel}|</a>') . ',"")}|{IF(company.tel > "",IF(contact.gsm > "","<br />","<span class=\"hidden-xs\">&nbsp;</span>"),"")}|{IF(ISNULL(contact.gsm),"",IF(contact.gsm > "",' . Mysql::concat('<a href="tel:|{contact.gsm}|">|{contact.gsm}|</a>') . ',""))}|',
//        ];
//        $items = [
//            '<strong>|{product.code}|</strong>',
//            '|{product.title}|',
//            '|{product.price}|&nbsp;€',
//            '<span class="hidden-xs">|{product.description}|</span>',
//        ];
//        $grid = (string) H::grid()->auto($items, count($items), true);
        
        if (is_array($columns)) {
            $cols = implode(', ', $columns);
        } else {
            $cols = 'id, uid, code, title, price, price_type, description, status, date_update';
        }
        
        $params = [];
        $sql  = 'SELECT ' . $cols . ' ';
        $sql .= 'FROM product ';
        $sql .= 'WHERE product.id_account=' . (int) I::getIdAccount() . ' ';
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND product.date_update >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND product.date_update <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        if (isset($settings['pi']) && $settings['pi']) {
            $sql .= 'AND product.price >= ? ';
            $params[] = $settings['pi'];
        }
        if (isset($settings['pa']) && $settings['pa']) {
            $sql .= 'AND product.price <= ? ';
            $params[] = $settings['pa'];
        }
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND (product.code LIKE ? OR product.title LIKE ? OR product.description LIKE ?) ';
            $like = Mysql::like($settings['q']);
            $params[] = $like;
            $params[] = $like;
            $params[] = $like;
        }
        $sql .= 'ORDER BY ' . $sortBy;
        return DB::getCompanyTable()->prepare($sql)->execute($params);
    }
    
    public static function getProductForForm(int $idProduct)
    {
        return DB::getProductTable()->find($idProduct)->toArray();
    }
    
    /**
     * Suppression d'un produit
     * @param int $idProduct
     * @return type
     */
    public static function deleteProduct(int $idProduct)
    {
        $result = DB::getProductTable()->delete(['id' => $idProduct, 'id_account' => I::getIdAccount()]);
        C::getSearch()->cleanAutocomplete(self::CATEGORY, $idProduct);
        return $result;
    }
    
    /**
     * Mise à jour d'un produit dans les données de recherche
     * @param int $id
     * @param array $values
     * @param bool $cleanItem
     */
    protected static function updateSearchIndex(int $id, array $values, bool $cleanItem = true, $idAccount = null)
    {
        $searchData = $values['uid'] . '. ' . $values['code'] . ' ' . $values['title'] . ' ' . $values['description'];
        $values['id'] = $id;
        $valsToInsert = Tab::reduce($values, self::SEARCH_FIELDS);
        if ($cleanItem || !$values['status']) {
            C::getSearch()->cleanAutocomplete(self::CATEGORY, $id, $idAccount);
        }
        if ($values['status']) {
            $title = $values['code'] . ' ' . $values['title'];
            $url = C::getRouter()->buildUri(['id' => $id], 'product', 'view');
            C::getSearch()->indexAutocompleteItem($searchData, $title, $valsToInsert, self::CATEGORY, $id, $url, 10, $idAccount);
        }
    }
    
    /**
     * Update products in search table
     */
    public static function updateAllProductsForSearchEngine(int $idAccount = null)
    {
        $idAccount = $idAccount ?: I::getIdAccount();
        C::getSearch()->clean(self::CATEGORY, $idAccount);
        $sql = 'SELECT ' . implode(', ', self::SEARCH_FIELDS) . ', description, status FROM product WHERE id_account=? AND status=1';
        $products = DB::getProductTable()->prepare($sql)->execute([$idAccount]);
        foreach ($products as $product) {
            self::updateSearchIndex($product['id'], $product, false, $idAccount);
        }
    }
    
    /**
     * Attache à l'élément select une autocomplétion sur les produits
     * @param ElementSelect $elt
     * @return ElementSelect
     */
    public function registerAutocomplete(ElementSelect $elt = null, ?int $limit = null): ElementSelect
    {
        $limit = $limit ?? $this->autocompleteLimit;
        
        // @task [AUTOCOMPLETE] Quand il y a des produits initiaux, c'est pratique 
        // pour un accès rapide aux produits "les plus utilisés" (todo) mais quand 
        // on veut tous les produits sortis avec un mot clé en particulier on ne 
        // peut pas maintenir dans la liste des suggestions uniquement ces produits, 
        // de telle sorte qu'on puisse les insérer tous en quelques clics
        $initialItems = $limit > 0 ? C::getSearch()->searchAutocomplete('', self::CATEGORY, $limit) : null;
        // $template = "'<div>' + item.uid + '. ' + '<strong>' + escape(item.code) + '</strong> '"
        $template = "'<div>' + '<strong>' + escape(item.code) + '</strong> '"
                . " + '<span class=\"pull-right\">' + escape(item.price) + ' '"
                . " + escape(item.price_type) + '</span>'"
                . " + '<br /></span>' + escape(item.title) + '</span>'"
                . " + '</div>'";
        $url = C::getViewHelper()->url('event', 'ac') . '/' . self::CATEGORY . '/';
        $elt = $elt ?: new ElementSelect(self::CATEGORY);
        $elt->setAutocomplete($url, $template, $initialItems);
        if (!$elt->getPlaceholder()) {
            if ($elt->isMultiple()) {
                $elt->setPlaceholder(__("Choisir un ou plusieurs produits"));
            } else {
                $elt->setPlaceholder(__("Choisir un produit"));
            }
        }
        return $elt;
    }
    
    /**
     * Récupération d'un flux JSON avec les produits les plus pertinents pour l'autocomplétion
     * @param string $phrase
     * @param int $count
     * @return string
     */
    public static function getAutocompleteItems(string $phrase = '', int $count = 10)
    {
        return C::getSearch()->searchAutocomplete($phrase, self::CATEGORY, $count);
    }
}
