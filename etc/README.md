# SimpleManager configuration files

## application.php

The general configuration file extends the default configuration.

```php
<?php 

return [
    'db' => [

        // Administration database
        'admin' => [
            'database' => '',
            'hostname' => '',
            'username' => '',
            'password' => ''
        ],

        // Craft database
        'common' => [
            'database' => '',
            'hostname' => '',
            'username' => '',
            'password' => ''
        ]
    ],

    // Redis configuration
    'redis' => [
        'host' => '',
        'auth' => ''
    ],


    // Email configuration
    'mail' => [
        'debug_mode' => 0,
        'smtp' => [
            'name' => '',
            'host' => '',
            'port' => 587,
            'connection_class' => 'plain',
            'connection_config' => [
                'username' => '',
                'password' => '',
                'ssl' => 'tls'
            ]
        ],
        
        // No reply contact
        'noreply' => ['mail' => 'noreply@' . APP_HOST, 'name' => APP_NAME],
        
        // Default from contact
        'from'    => ['mail' => 'contact@' . APP_HOST, 'name' => sprintf("Contact %s", APP_NAME)],
        
        // Default sender contact
        'sender'  => ['mail' => 'contact@' . APP_HOST, 'name' => sprintf("Contact %s", APP_NAME)],
        
        // Contact recipient for debugging
        'debug'   => ['mail' => '', 'name' => ''],
        
        // Administrator contact
        'admin'   => ['mail' => '', 'name' => sprintf("Contact %s", APP_NAME)]
    ]
];
```

Tip: you can create also `application.production.php`, `application.staging.php` and `application.development.php`.

## acl.yml

This file extends default ACL configuration.

```yaml
admin: 
  - your@email.com
  - anotheradmin@email.com
```

## Environment variables

Define these system environment variables as needed:

> **APP_NAME** (default: SimpleManager)
> **APP_SNAM** (default: SMA)
> **APP_HOST** (default: simplemanager.fr)
> **APPLICATION_ENV** (default: production) [production|staging|development]
