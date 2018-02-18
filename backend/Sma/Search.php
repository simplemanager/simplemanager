<?php
namespace Sma;

use Osf\Stream\Json;
use Osf\Exception\ArchException;
use Osf\View\Helper\Bootstrap\Tools\Checkers;
use Osf\Stream\Text as T;
use Sma\Session\Identity as I;
use DB;

/**
 * General search engine
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package sma
 * @subpackage search
 */
class Search
{
    const TAG_PAGE    = 'page';
    const TAG_DOC     = 'doc';
    const TAG_DOC_PDF = 'pdf';
    const TAG_USER    = 'user';
    const TAG_PRODUCT = 'product';
    const TAG_INFO    = 'info';
    const TAG_STATS   = 'stats';
    
    const TAGS = [
        self::TAG_PAGE,
        self::TAG_DOC,
        self::TAG_DOC_PDF,
        self::TAG_USER,
        self::TAG_PRODUCT,
        self::TAG_INFO,
        self::TAG_STATS
    ];
    
    const SEARCH_FIELDS = ['search.title', 'search.doc', 'search.params', 'search.url'];
    
    protected $actions = [];
    
    /**
     * Indexation
     * @param string $title titre de l'entrée pour affichage et recherche
     * @param int $level Niveau d'importance (1 à 100) ou tinyint
     * @param mixed $params paramètres / structure qui sera sérialisée
     * @param string $doc document long à indexer
     * @param array $tags liste de tags (strings) à lier
     * @param int $idAccount par défaut l'id account de l'utilisateur courant
     * @param $searchContent contenu de recherche spécifique
     * @return $this
     */
    public function index(
            string $title, 
            int    $level,
            array  $params,
                   $url = null,
            string $doc = null, 
            array  $tags = [], 
                   $idAccount = null,
            string $searchContent = null)
    {
        $idAccount = $idAccount ?: I::getIdAccount();
        $url = $url === null ? null : (string) $url;
        $this->actions[] = ['insert', $this->checkParameters(get_defined_vars())];
        return $this;
    }
    
    /**
     * Indexation spécifique aux données d'autocomplétion
     * @param string $searchData
     * @param array $item
     * @param string $category
     * @param int $id
     * @param int $level
     * @param int $idAccount
     * @return $this
     */
    public function indexAutocompleteItem(
            string  $searchData, 
            string  $title, 
            array   $item, 
            string  $category, 
            int     $id, 
            ?string $url = null, 
            int     $level = 10, 
            ?int    $idAccount = null, 
            string  $doc = '',
            array   $subCategories = [])
    {
        $item['search_content'] = $searchData;
        $tags = [$category, $category . $id];
        foreach ($subCategories as $subCategory) {
            $tags[] = $subCategory;
            $tags[] = $subCategory . $id;
        }
        return $this->index($title, $level, $item, $url, $doc, $tags, $idAccount, T::transliterate($searchData));
    }
    
    /**
     * Suppression de toutes les entrées avec le tag spécifié
     * @param string $tag
     * @return $this
     */
    public function clean(string $tag = null, $idAccount = null)
    {
        $this->actions[] = ['clean', $tag, $idAccount];
        return $this;
    }
    
    /**
     * Suppression de toutes les entrées avec au moins un des tags spécifiés (OU logique)
     * @param array $tags
     * @return $this
     */
    public function cleanAllTags(array $tags, $idAccount = null)
    {
        foreach ($tags as $tag) {
            $this->clean((string) $tag, $idAccount);
        }
        return $this;
    }
    
    /**
     * Suppression d'une entrée d'autocomplétion
     * @param string $category
     * @param int $id
     * @return $this
     */
    public function cleanAutocomplete(string $category, ?int $id = null, ?int $idAccount = null)
    {
        return $this->clean($category . $id, $idAccount);
    }
    
    /**
     * Search
     * @param string $phrase
     * @param string $tag
     * @return \Zend\Db\Adapter\Driver\Mysqli\Result
     */
    public function search(
            string  $phrase, 
            ?string $tag = null, 
            bool    $fullText = true, 
            int     $limit = 20, 
            int     $pageNo = 0, 
            array   $searchFields = self::SEARCH_FIELDS)
    {
        $values = [];
        $sql  = 'SELECT ' . implode(', ', $searchFields) . ' FROM search ';
        if ($tag !== null) {
            $sql .= 'JOIN search_tag ON search.id=search_tag.id_search '
                    . 'AND search_tag.tag=? ';
            $values[] = $tag;
        }
        $sql .= 'WHERE search.id_account=? AND title <> \'\' ';
        $values[] = I::getIdAccount();
        if ($fullText) {
            $sql .= 'AND MATCH (search.search_content) AGAINST (? IN BOOLEAN MODE) ';
            $values[] = T::transliterate($phrase) . '*';
        } else {
            $sql .= 'AND search.search_content LIKE ? ';
            $values[] = '%' . T::transliterate($phrase) . '%';
        }
        $sql .= 'ORDER BY search.level DESC, search.date_insert ASC '
              . 'LIMIT ' . (int) $pageNo . ',' . (int) $limit;
        return DB::getSearchTable()->prepare($sql)->execute($values);
    }
    
    /**
     * Search for autocomplete, return a JSON stream
     * @param string $phrase
     * @param string $tag
     * @param int $limit
     * @return string JSON feed
     */
    public function searchAutocomplete(string $phrase, string $tag, int $limit = 10)
    {
        $tag = $tag === '*' ? null : $tag;
        $rows = $this->search(T::transliterate($phrase), $tag, false, $limit, 0, ['search.params']);
        return $this->buildAutocompleteStream($rows);
    }
    
    /**
     * Recherche spécifique par ids
     * @param array $ids
     * @param string $tag
     * @return array tableau de chaînes JSON
     * @throws ArchException
     */
//    public function searchAutocompleteIds(array $ids, string $tag)
//    {
//        // Filtrage et validation des ids
//        $filteredIds = [];
//        foreach ($ids as $key => $id) {
//            if (!is_numeric($id)) {
//                throw new ArchException('Not a numeric id [' . $id . ']');
//            }
//            $filteredIds[] = (int) $id;
//        }
//        $strIds = implode(',', $filteredIds);
//        
//        // Récupération des données et renvoi
//        $sql  = 'SELECT search.id, search.params '
//                . 'FROM search '
//                . 'JOIN search_tag ON search.id=search_tag.id_search '
//                    . 'AND search_tag.tag=? '
//                . 'WHERE search.id_account=? '
//                . 'AND search.id IN (' . $strIds . ');';
//        $rows = DB::getSearchTable()->prepare($sql)->execute([$tag, I::getIdAccount()]);
//        return $this->buildAutocompleteStream($rows, true);
//    }
    
    /**
     * Transforme le résultat de la requête en flux JSON pour l'autocomplétion
     * @param Iterator $rows
     * @param bool $returnArrayOfJson
     * @return array|string array of json or json stream
     */
//    protected function buildAutocompleteStream($rows, bool $returnArrayOfJson = false)
//    {
//        $result = [];
//        foreach ($rows as $row) {
//            if (isset($row['id'])) {
//                $result[$row['id']] = $row['params'];
//            } else {
//                $result[] = $row['params'];
//            }
//        }
//        if ($returnArrayOfJson) {
//            return $result;
//        }
//        return '[' . implode(',', $result) . ']';
//    }
    
    /**
     * Transforme le résultat de la requête en flux JSON pour l'autocomplétion
     * @param Iterator $rows
     * @param bool $returnArrayOfJson
     * @return string json stream
     */
    protected function buildAutocompleteStream($rows)
    {
        $result = [];
        foreach ($rows as $row) {
            $result[] = $row['params'];
        }
        return '[' . implode(',', $result) . ']';
    }
    
    /**
     * Validation des paramètres d'insertion
     * @param array $parameters
     * @return int
     */
    protected function checkParameters(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            switch ($key) {
                case 'level' : 
                    if ($value === null) {
                        $parameters[$key] = 0;
                    } else if ($value < 1 || $value > 100) {
                        Checkers::notice('Priority value ' . $value . ' is not allowed');
                        $parameters[$key] = 0;
                    }
                    break;
                case 'idAccount' : 
                    if (!is_int($value) && !is_null($value)) {
                        throw new ArchException('Bad id account type [' . gettype($value) . ']');
                    }
                    break;
                case 'url' : 
                    if (!preg_match('#[a-z0-9/_-]+#', $value)) {
                        throw new ArchException('Bad url syntax [' . $value . ']');
                    }
                    break;
                default : 
                    //throw new ArchException('Parameter [' . $key . '] unknown');
            }
        }
        return $parameters;
    }
    
    /**
     * @return $this
     */
    public function commit()
    {
        foreach ($this->actions as $action) {
            $data = $action[1];
            switch ($action[0]) {
                
                // Insertion en base
                case 'insert' : 
                    $table = DB::getSearchTable();
                    if ($data['searchContent'] === null) {
                        $ttit = T::transliterate($data['title']);
                        $tdoc = T::transliterate($data['doc']);
                        $data['searchContent'] = $ttit . ' ' . $ttit . ' ' . $tdoc;
                    }
                    $table->insert([
                        'title'          => $data['title'],
                        'doc'            => $data['doc'],
                        'search_content' => $data['searchContent'],
                        'level'          => $data['level'],
                        'url'            => $data['url'],
                        'params'         => Json::encode($data['params'], false),
                        'id_account'     => $data['idAccount']
                    ]);
                    $id = $table->getLastInsertValue();
                    foreach ($data['tags'] as $tag) {
                        DB::buildSearchTagRow()
                                ->setIdSearch($id)
                                ->setIdAccount($data['idAccount'])
                                ->setTag($tag)->insert();
                    }
                    break;
                
                // Nettoyage
                case 'clean' :
                    
                    // récupération du Id Account
                    $cleanIdAccount = is_int($action[2]) ? $action[2] : I::getIdAccount();
                    
                    // Suppression des données de recherche
                    $values = [$cleanIdAccount];
                    $sql = 'DELETE FROM `search` '
                            . 'WHERE id_account=? ';
                    if ($data !== null) {
                        $sql .= 'AND id IN (SELECT search_tag.id_search '
                                . 'FROM search_tag '
                                . 'WHERE search_tag.tag=?)';
                        $values[] = (string) $data;
                    }
                    DB::getSearchTable()->prepare($sql)->execute($values);
                    
                    // Suppression des tags orphelins de l'idAccount demandé
                    $sql = 'DELETE FROM search_tag '
                            . 'WHERE id_account=? '
                            . 'AND id_search NOT IN ('
                            . 'SELECT id '
                            . 'FROM search WHERE id_account=?)';
                    DB::getSearchTagTable()->prepare($sql)->execute([$cleanIdAccount, $cleanIdAccount]);
                    break;
            }
        }
        $this->actions = [];
        return $this;
    }
    
    public function __destruct()
    {
        $this->commit();
    }
}
