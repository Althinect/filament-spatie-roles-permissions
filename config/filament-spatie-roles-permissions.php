<?php

return [

    'preload_roles' => false,

    'preload_permissions' => false,

    'generator' => [

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
         * returns the "name" for the permission.
         * $permission which is an iteration of [model_permissions] ('view-any','view','create','update','delete','restore','force-delete'),
         * $model The model to which the $permission will be interpolated
         *
         * Eg: 'return Str::ucfirst($model) .\' \'. $permission;'
         * 
         */
        'permission_name' => 'return $permission. \' \' .$model;',

        /*
         * Permissions will be generated ONLY for the models associated with the respective Filament Resources
         */
        'discover_models_through_filament_resources' => false,

        /*
         * Include custom model directories here.
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
    ]
];
