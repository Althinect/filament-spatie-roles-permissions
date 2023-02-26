<?php

return [

    'preload_roles' => false,

    'preload_permissions' => false,

    'generator' => [

        'guard_names' => [
            'web',
            //'api'
        ],

        'permission_affixes' => [

            /*
             * Permissions Aligned with Policies.
             * DO NOT change the keys unless the genericPolicy.stub is published and altered accordingly
             */
            'viewAnyPermission' => 'view-any',
            'viewPermission' => 'view',
            'createPermission' => 'create',
            'updatePermission' => 'update',
            'deletePermission' => 'delete',
            'restorePermission' => 'restore',
            'forceDeletePermission' => 'force-delete',

            /*
             * Additional Resource Permissions
             */
            'replicate',
            'reorder',
        ],

        /*
         * returns the "name" for the permission.
         *
         * $permission which is an iteration of [permission_affixes] ,
         * $model The model to which the $permission will be concatenated
         *
         * Eg: 'permission_name' => fn($permissionAffix, $model) => $permissionAffix . ' ' . Str::kebab($model),
         *
         * Note: If you are changing the "permission_name" , It's recommended to run with --clean to avoid duplications
         */
        'permission_name' => fn($permissionAffix, $model) => $permissionAffix . ' ' . $model,

        /*
         * Permissions will be generated ONLY for the models associated with the respective Filament Resources
         */
        'discover_models_through_filament_resources' => false,

        /*
         * Include directories which consists of models.
         */
        'model_directories' => [
            /*
             * path => namespace
             */
            app_path('Models') => 'App\Models'
            //app_path('Domains/Forum') => 'Domains\Forum\Models'
        ],

        /*
         * Define custom_models in snake-case
         */
        'custom_models' => [

        ],

        /*
         * Define excluded_models in snake-case
         */
        'excluded_models' => [
            //
        ],

        'excluded_policy_models' => [
            \App\Models\User::class
        ],

        /*
         * Define any other permission here
         */
        'custom_permissions' => [
            //'view-log'
        ],


        'user_model' => \App\Models\User::class,

        'policies_namespace' => 'App\Policies'
    ],
];
