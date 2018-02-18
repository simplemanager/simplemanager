<?php

// 
// General SMA configuration
// 
// Do not update this file !
// 
// Create a .application.php and/or .application.[env].php file in this directory returning 
// an array merged with this file. 
// 

$config = [
    'db' => [
        'admin' => [
            'driver' => 'Mysqli',
            'database' => '',
            'hostname' => '',
            'username' => '',
            'password' => '',
            'charset'  => 'utf8mb4',
            'driver_options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8MB4",
            ],
//            'dbcollation' => 'utf8mb4_unicode_ci',
        ],
        'common' => [
            'driver' => 'Mysqli',
            'database' => '',
            'hostname' => '',
            'username' => '',
            'password' => '',
            'charset'  => 'utf8mb4',
            'driver_options' => [
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8MB4"
            ],
//            'dbcollation' => 'utf8mb4_unicode_ci',
        ]
    ],
    'layout' => APPLICATION_PATH . '/App/Common/View/layouts/main.phtml',
    'mail' => [
        
        // No reply contact
        'noreply' => ['mail' => 'noreply@' . APP_HOST, 'name' => APP_NAME],
        
        // Default from contact
        'from'    => ['mail' => 'contact@' . APP_HOST, 'name' => sprintf("Contact %s", APP_NAME)],
        
        // Default sender contact
        'sender'  => ['mail' => 'contact@' . APP_HOST, 'name' => sprintf("Contact %s", APP_NAME)],
        
        // Contact recipient for debugging
        'debug'   => ['mail' => '', 'name' => ''],
        
        // Administrator contact
        'admin'   => ['mail' => '', 'name' => sprintf("Contact %s", APP_NAME)],
        
        // SMTP to use to send emails
        'smtp'    => ['name' => '', 'host' => '', 'port' => 25]
    ]
];

$localConfig = __DIR__ . '/.application.php';
if (file_exists($localConfig)) {
    $config = array_replace_recursive($config, include($localConfig));
}
$envConfig = __DIR__ . '/.application.' . APPLICATION_ENV . '.php';
if (file_exists($envConfig)) {
    $config = array_replace_recursive($config, include($envConfig));
}

return $config;
