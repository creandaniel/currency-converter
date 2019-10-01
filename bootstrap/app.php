<?php

use \App\Kernel\App;

require 'kernel.php';

session_start();

$app = new App(['settings' => require config_path() . '/app.php']);

$app->registerServices();

$app->registerAppMiddlewares();

require app_path() . '/Routes/app.php';


// Service factory for the ORM
$container['db'] = function ($container) {
    $capsule = new \Illuminate\Database\Capsule\Manager;
    $capsule->addConnection($container['settings']['db']);

    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};



$app->run();
