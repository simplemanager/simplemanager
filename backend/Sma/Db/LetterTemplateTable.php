<?php
namespace Sma\Db;

use Osf\Exception\ArchException;
use Osf\Stream\Text as T;
use Osf\Helper\Mysql;
use Sma\Session\Identity;
use Sma\Db\Generated\AbstractLetterTemplateTable;
use Sma\Log;
use App\Document\Model\LetterTemplate\LetterTemplateManager as LTM;
use ACL;

/**
 * Table model for table letter_template
 *
 * Use this class to complete AbstractLetterTemplateTable
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class LetterTemplateTable extends AbstractLetterTemplateTable
{
    use Addon\SafeActions;
    
    /**
     * Enregister le template et retourne l'id correspondant
     * @param array $values
     * @param int $id
     * @return int
     * @throws ArchException
     */
    public function saveTemplate(array $values, $id = null)
    {
        $idAccount = Identity::getIdAccount();
        $values['id_account'] = $idAccount;
        $values['search_data'] = $this->buildSearchData($values);
        if ($id !== null) {
            $row = $this->select(['id_account' => $idAccount, 'id' => (int) $id])->current();
            if ($row instanceof LetterTemplateRow) {
                $values['id'] = (int) $id;
                $row->populate($values, true)->save();
            } else {
                $msg = 'Unable to find template [' . (int) $id . '] for account [' . $idAccount . ']';
                Log::hack($msg, $values);
                throw new ArchException($msg);
            }
        } else {
            $this->insert($values);
            $id = $this->getLastInsertValue();
        }
        return (int) $id;
    }
    
    /**
     * Construit les données de recherche liées au template
     * @param array $values
     * @return string
     */
    public function buildSearchData(array $values)
    {
        return T::transliterate($values['title'] . ' ' . $values['description']);
    }
    
    
    public function updateDocument(DocumentInterface $doc, int $id)
    {
        // Mise à jour du document
        $docMain = $this->getDocumentRow($doc);
        $doc->getBean()->setId($id);
        $this->update($docMain, ['id' => $id, 'id_account' => Identity::getIdAccount()]);
        
        // Ajout dans l'historique
        $this->addDocHistory($doc, $id);
        
        // Mise à jour des données de recherche
        self::updateSearchIndex($doc->getBean(), $doc->getType());
    }
    
    public function getTemplates(array $settings = [])
    {
        $sorts = [
            'ta'  => 'title ASC',
            'td'  => 'title DESC',
//            'da'  => 'description ASC',
//            'dd'  => 'description DESC',
//            'dca' => 'date_insert ASC',
//            'dcd' => 'date_insert DESC',
            'dua' => 'date_update ASC',
            'dud' => 'date_update DESC',
        ];
        $sort = isset($settings['s']) && isset($sorts[$settings['s']]) ? $sorts[$settings['s']] : $sorts['ta'];
        
        $settings['categories'] = ['common'];
        
        $params = [];
        $sql = 'SELECT * FROM ' . $this->getTableName() . ' WHERE 1 ';
        
        // Catégories en fonction du profil
        if (ACL::isAdmin()) {
            $sql .= 'AND ((category = \'mine\' AND id_account=?) OR category != \'mine\') ';
            $params[] = Identity::getIdAccount();
        } else {
            $sql .= 'AND ((category = \'mine\' AND id_account=?)';
            $params[] = Identity::getIdAccount();
            if (isset($settings['categories']) && $settings['categories']) {
                foreach ($settings['categories'] as $category) {
                    if ($category == 'mine') { continue; }
                    $sql .= ' OR category = ?';
                    $params[] = $category;
                }
            }
            $sql .= ') ';
        }
        if (isset($settings['dt']) && $settings['dt']) {
            $sql .= 'AND data_type = ? ';
            $params[] = $settings['dt'];
        }
        if (isset($settings['ca']) && $settings['ca']) {
            $sql .= 'AND category = ? ';
            $params[] = $settings['ca'];
        }
        if (isset($settings['id']) && $settings['id']) {
            $sql .= 'AND id = ? ';
            $params[] = (int) $settings['id'];
        }
        if (isset($settings['f']) && $settings['f']) {
            $sql .= 'AND date_update >= ? ';
            $params[] = Mysql::dateToMysql($settings['f']);
        }
        if (isset($settings['t']) && $settings['t']) {
            $sql .= 'AND date_update <= ? ';
            $params[] = Mysql::dateToMysql($settings['t']) . ' 23:99:99';
        }
        if (isset($settings['q']) && $settings['q'] !== '') {
            $sql .= 'AND (search_data LIKE ?) ';
            $like = Mysql::like(T::transliterate($settings['q']));
            $params[] = $like;
//            $sql .= 'AND MATCH (search_data) AGAINST (? IN BOOLEAN MODE) ';
//            $params[] = '*' . T::transliterate($settings['q']) . '*';
        }
        $sql .= ' ORDER BY ' . $sort;
        return $this->prepare($sql)->execute($params);
    }
    
    /**
     * Liste des catégories existantes
     * @return array
     */
    public function getCategories(bool $withTitle = true)
    {
        $sql = 'SELECT DISTINCT category '
                . 'FROM ' . $this->getTableName() . ' '
                . 'WHERE category NOT IN (\'' . implode('\', \'', array_keys(LTM::getCategoryLabels())) . '\') '
                . 'ORDER BY category ASC';
        $rows = $this->execute($sql);
        $categories = LTM::getCategoryLabels($withTitle);
        foreach ($rows as $value) {
            $categories[$value['category']] = $value['category'];
        }
        return $categories;
    }
    
    /**
     * Recherche un template dont l'utilisateur courant à le droit de lire
     * @param int $id
     * @return LetterTemplateRow
     */
    public function getTemplateForRead($id)
    {
        if ($id && is_numeric($id)) {
            $rows = $this->getTemplates(['id' => (int) $id]);
            if ($rows->count() === 1) {
                return $rows->current();
            }
        }
        return null;
    }
    
    /**
     * Couleur, niveau, icone, accès en écriture d'une catégorie
     * @param string $category
     * @return array
     */
    public static function getCategoryDecorations(string $category)
    {
        switch (true) {
            case $category === 'mine' : 
                $color = 'green';
                $level = 'success';
                $icon  = 'file-code-o';
                $writable = true;
                break;
            case $category === 'common' : 
                $color = 'blue';
                $level = 'info';
                $icon  = 'file-code-o';
                $writable = ACL::isAdmin();
                break;
            case $category === 'draft' : 
                $color = 'gray';
                $level = 'default';
                $icon  = 'file-code-o';
                $writable = ACL::isAdmin();
                break;
            default : 
                $color = 'light-blue';
                $level = 'primary';
                $icon  = 'file-code-o';
                $writable = ACL::isAdmin();
        }
        return [
            'color'    => $color, 
            'level'    => $level, 
            'icon'     => $icon, 
            'writable' => $writable
        ];
    }
}