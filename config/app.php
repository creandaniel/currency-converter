<?php

return [
    'displayErrorDetails' => env('APP_DEBUG', false),
    'determineRouteBeforeAppMiddleware' => false,
    // 'routerCacheFile' => storage_path() . '/cache/routes.php',

        'displayErrorDetails' => true,
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'database',
            'username' => 'root',
            'password' => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

    'middlewares' => require 'middlewares.php',

    'services' => require 'services.php',

    'logger' => require 'logger.php',

    'twig' => require 'twig.php',
];

$container = $app->getContainer();
//boot eloquent connection
$container->get('db');

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($container['settings']['db']);
$capsule->setAsGlobal();
$capsule->bootEloquent();
//pass the connection to global container (created in previous article)
$container['db'] = function ($container) use ($capsule){
    global $capsule;
   return $capsule;
};
