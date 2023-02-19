<?php

return [

    'preload_roles' => false,

    'preload_permissions' => false,

    'guard_names' => [
        'web',
        //'api'
    ],

    'model_permissions' => [
        'view-any',
        'view',
        'create',
        'update',
        'delete',
        'restore',
        'force-delete'
    ],

    /*
     * Permissions will be generated only for the models associated with the respective Filament Resources
     */
    'discover_models_through_filament_resources' => false,

    /*
     * If you have custom model directories, include them here.
     */
    'model_directories' => [
        'Models',
        //'Domains/Posts/Models'
    ],

    /*
     * Define custom_models in snake-case
     */
    'custom_models' => [
        //'roles',
        //'permissions'
    ],

    /*
     * Define excluded_models in snake-case
     */
    'excluded_models' => [
        'team',
    ],

    /*
     * Define any other permission here
     */
    'custom_permissions' => [
        //'log.view'
    ]
];
