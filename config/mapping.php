<?php return [
    /*
    |--------------------------------------------------------------------------
    | Custom mapping classes.
    |--------------------------------------------------------------------------
    |
    | This value should be a key value array mapping from custom mapping
    | classes to their relevant source and target classes. They are to
    | be automatically registered with the AutoMapperConfiguration.
    */
    'custom' => [
        /* 'mapperClass' => [
            'source' => 'sourceClass',
            'target' => 'targetClass'
        ] */
    ],

    /*
     |--------------------------------------------------------------------------
     | Directory scan for mappers.
     |--------------------------------------------------------------------------
     |
     | Configure app subdirectories to be scanned for custom mapping classes.
     | Mappings found (using PSR-4 naming) that have the correct interface
     | and mapping attribute are automatically registered to the config.
     */
    'scan' => [
        // Flag to disable / enable scan
        'enabled' => env('AUTO_MAPPER_SCAN_ENABLED', false),

        // App subdirectories to scan
        'dirs' => [
            // 'Mappings'
        ]
    ],

    /*
     |--------------------------------------------------------------------------
     | Cache mapping config.
     |--------------------------------------------------------------------------
     |
     | Configure a path where Custom Mappers may be stored to be retrieved later
     | instead of having to scan mapping directories on every request to the
     | web server. This should be enabled for a slight performance boost.
     */
    'cache' => [
        // Determine if mappings should be cached
        'enabled' => env('AUTO_MAPPER_CACHE', false),
        
        // Directory to store cached mappers.
        'dir' => env('AUTO_MAPPER_CACHE_DIR', storage_path('app/framework/automapper')),

        // Default cache key for mappings
        'key' => env('AUTO_MAPPER_CACHE_KEY', 'automapper.php')
    ]
];
