<?php

header('Content-type: text/html; charset=utf-8');

$extRequired = [
    'date',
    'dom',
    'fileinfo',
    'filter',
    'gd',
    'gettext',
    'hash',
    'iconv',
    'imagick',
    'intl',
    'json',
    'libxml',
    'mbstring',
    'mcrypt',
    'mysqli',
    'mysqlnd',
    'openssl',
    'pcre',
    'redis',
    'Reflection',
    'session',
    'SimpleXML',
    'SPL',
    'xml',
    'zip',
    'zlib'
];

$extOption = [
    'igbinary',
    'PDO',
    'pdo_mysql',
    'pdo_sqlite',
    'Phar',
    'sqlite3',
    'yaml',
    'Zend OPcache',
];

function displayExts(array $exts)
{
    foreach ($exts as $ext) {
        $mark = extension_loaded($ext) 
                ? '  <span style="color: green">FOUND</span>  '
                : '<span style="color: red"><strong>NOT FOUND</strong></span>';
        printf("%'.-20s [ %s ]\n", $ext, $mark);
    }
}

echo '<pre>';

echo '<strong>Extensions requises:</strong><br />';
displayExts($extRequired);

echo '<br /><strong>Extensions importantes:</strong><br />';
displayExts($extOption);

echo '</pre>';