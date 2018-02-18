<?php
namespace App\Dev\Model;

use Osf\Container\ZendContainer as ZendContainer;

/**
 * Db tools
 *
 * @author Guillaume Ponçon <guillaume.poncon@openstates.com>
 * @copyright OpenStates
 * @version 1.0
 * @since OSF-2.0 - 2017
 * @package app
 * @subpackage dev
 */
class Db 
{
    public static function getColumnDefinition(string $schema, string $table, string $column)
    {
        $adapter = ZendContainer::getDbAdapter($schema);
        return $adapter->query("SELECT CONCAT('`', 
      CAST(COLUMN_NAME AS CHAR),
      '` ',
      CAST(COLUMN_TYPE AS CHAR),
      IF(ISNULL(CHARACTER_SET_NAME),
         '',
         CONCAT(' CHARACTER SET ', CHARACTER_SET_NAME)),
      IF(ISNULL(COLLATION_NAME), '', CONCAT(' COLLATE ', COLLATION_NAME)),
      ' ',
      IF(IS_NULLABLE = 'NO', 'NOT NULL ', ''),
      IF(IS_NULLABLE = 'NO' AND ISNULL(COLUMN_DEFAULT),
         '',
         CONCAT('DEFAULT ', QUOTE(COLUMN_DEFAULT), ' ')),
      UPPER(extra))
      AS column_definition
 FROM INFORMATION_SCHEMA.COLUMNS
WHERE  TABLE_SCHEMA = ?
   AND TABLE_NAME = ?
   AND COLUMN_NAME = ?;
", [$schema, $table, $column])->toArray()[0]['column_definition'];
    }
}