<?php
namespace Sma\Db;

use Sma\Db\Generated\AbstractDbContainer;
use Osf\Stream\Text as T;

/**
 * Table models container (writable)
 *
 * @version 1.0
 * @author Guillaume Ponçon - OpenStates Framework PHP Generator
 * @since OSF 2.0
 * @package osf
 * @subpackage generated
 */
class DbContainer extends AbstractDbContainer
{
    /**
     * Build table from its name
     * @param string $tableName
     * @return \Osf\Db\Table\AbstractTableGateway
     */
    public static function getTable(string $tableName)
    {
        $method = 'get' . T::camelCase($tableName) . 'Table';
        if (!method_exists(__CLASS__, $method)) {
            throw new \Osf\Exception\ArchException('Table [' . $tableName . '] is not valid.');
        }
        return self::$method();
    }
}