<?php

return [
    'navigation' => [
        'group' => 'Access Control',
    ],

    'resource' => [
        'role' => [
            'label' => 'Role',
            'plural_label' => 'Roles',
        ],
        'permission' => [
            'label' => 'Permission',
            'plural_label' => 'Permissions',
        ],
    ],

    'section' => [
        'role_details' => 'Role Details',
        'permissions' => 'Permissions',
    ],

    'field' => [
        'name' => 'Name',
        'guard_name' => 'Guard Name',
        'permissions' => 'Permissions',
        'permission_name' => 'Permission Name',
        'group' => 'Group',
        'role' => 'Role',
        'roles' => 'Roles',
        'team' => 'Team',
        'created_at' => 'Created At',
    ],

    'filter' => [
        'guard_name' => 'Guard Name',
        'group' => 'Group',
        'team' => 'Team',
    ],

    'action' => [
        'assign_permissions' => 'Assign permissions to role',
    ],

    'message' => [
        'permissions_assigned' => 'Permissions assigned successfully.',
        'guard_mismatch' => 'All selected permissions must use the same guard as the selected role.',
    ],

    'group' => [
        'ungrouped' => 'Ungrouped',
    ],
];
