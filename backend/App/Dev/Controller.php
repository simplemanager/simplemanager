<?php
namespace App\Dev;

use Zend\Db\Metadata\Metadata;
use Osf\Exception\DisplayedException;
use Osf\Container\ZendContainer;
//use Osf\View\Table;
use Osf\Stream\Text;
use Osf\Stream\Yaml;
use Sma\Controller\Json as JsonAction;
use Sma\Log;
use App\Common\Container;
use App\Dev\Model\Db as AppDb;
use H, DB, L;

/**
 * Espace administration
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 16 nov. 2013
 * @package company
 * @subpackage controllers
 */
class Controller extends JsonAction
{
    public function indexAction()
    {
    }
    
    public function dbAction()
    {
        H::layout()->setPageTitle(__("Base de données"));
        $comment = filter_input(INPUT_POST, 'comment');
        if ($comment !== null) {
            $this->disableView();
            try {
                $comment = trim(preg_replace("/\r\n/", "\n", $comment));
                if (strlen($comment) > 1024) {
                    throw new DisplayedException('Comment too long: ' . strlen($comment) . ' > 1024');
                }
                if ($comment && !@Yaml::parse($comment)) {
                    throw new DisplayedException('Comment syntax error: ' . error_get_last()['message']);
                }
                $field = filter_input(INPUT_POST, 'field');
                if (!$field || !preg_match('/^[a-z_]+\.[a-z_]+\.[a-z_]+$/', $field)) {
                    throw new DisplayedException('Bad parameters, unable to perform operation');
                }
                [$schema, $tableName, $fieldName] = explode('.', $field);
                $table = DB::getTable($tableName);
                $fields = $table->getFields();
                if (!isset($fields[$fieldName])) {
                    throw new DisplayedException('Field [' . $fieldName . '] not found');
                }
                $adapter = ZendContainer::getDbAdapter($schema);
                $sql  = 'ALTER TABLE `' . $tableName . '` CHANGE `' . $fieldName . '` ';
                $sql .= AppDb::getColumnDefinition($schema, $tableName, $fieldName);
                $sql .= ' COMMENT ' . $adapter->getPlatform()->quoteValue($comment);
                $adapter->query($sql)->execute();
                $title = 'Field ' . $tableName . ' :: ' . $fieldName . ' update';
                H::layout()->addAlert($title, 'SUCCESS: ' . $sql, L::STATUS_SUCCESS);
                H::layout()->addAlert('Generate db with CLI tool to commit changes', null, L::STATUS_WARNING);
                $this->layout()->forceRefreshBody(false);
            } catch (DisplayedException $e) {
                H::layout()->addAlert('Exception launched', $e->getMessage(), L::STATUS_DANGER);
            }
            return;
        }
        
        // Db management
        if ($this->hasParam('table')) {
            $tableItems = explode('.', $this->getParam('table'));
            $table = DB::getTable($tableItems[1]);
            $md = file_get_contents(__DIR__ . '/View/db-doc.md');
            $doc = Container::getMarkdown()->text($md);
            return [
                'table' => $table,
                'tableTitle' => $this->getParam('table'),
                'doc' => $doc
            ];
        }
        
        // List of tables
        $dbKeys = array_keys(Container::getConfig()->toArray()['db']);
        $tableList = ['' => '-- SELECT A TABLE --'];
        foreach ($dbKeys as $dbKey) {
            $adapter = DB::getDbAdapterFromKey($dbKey);
            $tables = (new Metadata($adapter))->getTableNames();
            foreach ($tables as $tableName) {
                $key = $dbKey . '.' . $tableName;
                $tableList[$key] = strtoupper(str_replace('.', ' :: ', $key));
            }
        }
        return ['tables' => $tableList];
    }
    
    /**
     * Gestion des commentaires
     */
    public function commentAction()
    {
        $this->disableLayout();
        $comment = trim(filter_input(INPUT_POST, 'comment'));
        if (strlen($comment) > 1 && $comment !== 'reset') {
            Log::info(Text::crop($comment, 255), 'COMMENT');
        }
        return ['comment' => $comment];
    }
}
