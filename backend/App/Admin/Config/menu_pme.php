<?php

return array(
    'home' => array(
        'label' => __("Accueil"),
        'params' => array()
    ),
    'billing' => array(
        'label' => __("Devis & Factures"),
        'params' => array(
            'controller' => 'billing'
        ),
        'items' => array(
            'billmanage' => array(
                'label' => __("Parcourir et gérer"),
                'params' => array(
                    'action' => 'manage'
                )
            ),
            'newquote' => array(
                'label' => __("Nouveau devis"),
                'params' => array(
                    'action' => 'quotation'
                )
            ),
            'newcommand' => array(
                'label' => __("Nouvelle commande"),
                'params' => array(
                    'action' => 'command'
                )
            ),
            'newinvoice' => array(
                'label' => __("Nouvelle facture"),
                'params' => array(
                    'action' => 'invoice'
                )
            )
        )
    ),
    'company' => array(
        'label' => __("Mon entreprise"),
        'params' => array(
            'controller' => 'company'
        ),
        'items' => array(
            'info' => array(
                'label' => __("Informations"),
                'params' => array(
                    'action' => 'info'
                )
            ),
            'clients' => array(
                'label' => __("Clients & Prospects"),
                'params' => array(
                    'action' => 'clients'
                )
            ),
            'products' => array(
                'label' => __("Produits"),
                'params' => array(
                    'action' => 'products'
                )
            ),
            'settings' => array(
                'label' => __("Réglages"),
                'params' => array(
                    'action' => 'settings'
                )
            ),
            'results' => array(
                'label' => __("Résultats"),
                'params' => array(
                    'action' => 'results'
                )
            ),
            'strategy' => array(
                'label' => __("Stratégie"),
                'params' => array(
                    'action' => 'strategy'
                )
            )
        )
    ),
    'process' => array(
        'label' => __("Procédures"),
        'params' => array(
            'controller' => 'process'
        ),
        'items' => array(
            'creation' => array(
                'label' => __("Déclarer mon activité"),
                'params' => array(
                    'action' => 'creation'
                )
            ),
            'taxes' => array(
                'label' => __("Déclarer mes impôts"),
                'params' => array(
                    'action' => 'taxes'
                )
            )
        )
    ),
    'configuration' => array(
        'label' => __("Configuration"),
        'params' => array(
            'controller' => 'configuration'
        ),
        'items' => array(
            'apps' => array(
                'label' => __("Mes applications"),
                'params' => array(
                    'action' => 'apps'
                )
            ),
            'interface' => array(
                'label' => __("Mon interface"),
                'params' => array(
                    'action' => 'interface'
                )
            ),
            'clientaccess' => array(
                'label' => __("Accès clients"),
                'params' => array(
                    'action' => 'client'
                )
            ),
            'accountantaccess' => array(
                'label' => __("Accès comptable"),
                'params' => array(
                    'action' => 'accountant'
                )
            )
        )
    ),
    'test' => array(
        'label' => 'Tests techniques',
        'icon' => 'fa-cogs',
        'params' => array(
            'controller' => 'test'
        )
    )
);