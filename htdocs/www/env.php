<?php

// General constants
defined('APP_PATH') || define('APP_PATH', getenv('APP_PATH') ?: realpath(__DIR__ . '/../..'));
defined('APP_NAME') || define('APP_NAME', getenv('APP_NAME') ?: 'SimpleManager');
defined('APP_SNAM') || define('APP_SNAM', getenv('APP_SNAM') ?: 'SMA'); // Initiales
defined('APP_HOST') || define('APP_HOST', getenv('APP_HOST') ?: 'simplemanager.fr'); // Nom d'hôte sans le sous-domaine

// Application path constant
if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', APP_PATH . '/backend');
}

// Autoloading
include_once APP_PATH . '/vendor/autoload.php';

// Error reporting
ini_set('display_errors', (int) APPLICATION_ENV === 'development');
ini_set('error_reporting', APPLICATION_ENV === 'development' ? E_ALL | E_STRICT : E_WARNING | E_ERROR);
