<?php

// Maintenance
defined('MAINTENANCE_MODE') || define('MAINTENANCE_MODE', getenv('MAINTENANCE_MODE') ?: false);
if (MAINTENANCE_MODE) {
    session_start();
    if (filter_input(INPUT_SERVER, 'QUERY_STRING') === 'startmtn') {
        $_SESSION['BYPASS'] = 1;
    } else if (filter_input(INPUT_SERVER, 'QUERY_STRING') === 'stop') {
        session_destroy();
    } else if (!$_SESSION['BYPASS']) {
        echo "SimpleManager est en cours de maintenance. Nous vous prions de revenir un peu plus tard.";
        exit;
    }
}

// Environnement d'exécution
defined('APPLICATION_ENV') || define('APPLICATION_ENV', getenv('APPLICATION_ENV') ?: 'production');

// Vérification SSL en production
#if (APPLICATION_ENV === 'production' && !filter_input(INPUT_SERVER, 'HTTPS')) {
#    header('Location: https://' . filter_input(INPUT_SERVER, 'HTTP_HOST') . filter_input(INPUT_SERVER, 'REQUEST_URI'));
#    exit;
#}

// Filtre des fichiers statiques
define('STATIC_URL_REGEX', '#^(.*document/dl/k/.+|.*(\.(pdf|png|jpg|html|ico|csv|xls|xlsx|ods)|/nl))$#');

// Lancement du layout
if (filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') !== 'XMLHttpRequest' && 
    !preg_match(STATIC_URL_REGEX, filter_input(INPUT_SERVER, 'REQUEST_URI'))) {
    include __DIR__ . '/layout.html';
    exit;
}

// Please install message
if (!file_exists(__DIR__ . '/../../etc/acl.yml')) {
    header('Content-type: application/json');
    echo '{"u":{"page":{"alerts":[{"title":"Application not installed","message":"Please install SimpleManager using the command line tool.","status":"warning","closable":false}]}}}';
    die();
}

// Load environment
require_once __DIR__ . '/env.php';

// Run the application
Osf\Container\OsfContainer::getApplication()->run();

