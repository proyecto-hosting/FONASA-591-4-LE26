<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // DISCO LOCAL PARA PRUEBAS (En Project IDX)
        'fonasa_local' => [
            'driver' => 'local',
            'root' => storage_path('app/fonasa_inbox'),
            'throw' => false,
        ],

        // PLACEHOLDER PARA PRODUCCIÓN (Google Cloud Storage)
        'fonasa_gcs' => [
            'driver' => 'gcs',
            'key_file_path' => env('GOOGLE_CLOUD_KEY_FILE', null), // Credenciales (si usas Service Account)
            'key_file' => [], // Alternativa si pasas el JSON directo
            'project_id' => env('GOOGLE_CLOUD_PROJECT_ID', 'nexo-host-666'), 
            'bucket' => env('GOOGLE_CLOUD_STORAGE_BUCKET', 'fonasa-remesas-inbox'),
            'path_prefix' => env('GOOGLE_CLOUD_STORAGE_PATH_PREFIX', ''),
            'storage_api_uri' => null, 
            'apiEndpoint' => null, 
            'visibility' => 'private', 
            'throw' => false,
        ],

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        'fonasa' => [
            // Si en el .env dice 'local', usa el disco duro. Si dice 'sftp', usa la red.
            'driver' => env('FONASA_DISK_DRIVER', 'local'), 
            
            // Ruta para cuando es local
            'root' => env('FONASA_DISK_DRIVER', 'local') === 'local' 
                        ? storage_path('app/sftp_mock') 
                        : '/ruta/remota/entregas', // Ruta real de FONASA en el futuro
            
            // Credenciales para cuando pasemos a Producción (se ignoran en local)
            'host' => env('FONASA_SFTP_HOST'),
            'username' => env('FONASA_SFTP_USERNAME'),
            'password' => env('FONASA_SFTP_PASSWORD'),
            'port' => env('FONASA_SFTP_PORT', 22),
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
