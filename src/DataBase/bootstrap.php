<?php

use Illuminate\Database\Capsule\Manager as Capsule;

$capsule = new Capsule;

$capsule->addConnection([
    'driver'    => getenv('DB_DRIVER'),
    'host'      => getenv('DB_HOST'),
    'database'  => getenv('DB_NAME'),
    'username'  => getenv('DB_USERNAME'),
    'password'  => getenv('BD_SECRET'),
]);

// Make this Capsule instance available globally via static methods... 
$capsule->setAsGlobal();

// Setup the Eloquent ORM... 
$capsule->bootEloquent();


