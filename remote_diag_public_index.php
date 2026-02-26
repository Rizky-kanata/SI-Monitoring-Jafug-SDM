<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
$autoloadPath = __DIR__.'/../vendor/autoload.php';

if (! file_exists($autoloadPath)) {
    $autoloadPath = __DIR__.'/../vendor-diagram/autoload.php';
}

require $autoloadPath;


// Fallback aliases for optional dev-only providers that may be absent on production deploys.
foreach ([
    'Laravel\Pail\PailServiceProvider',
    'Laravel\Sail\SailServiceProvider',
    'NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider',
] as $missingProvider) {
    if (! class_exists($missingProvider)) {
        class_alias(\Illuminate\Support\ServiceProvider::class, $missingProvider);
    }
}

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
