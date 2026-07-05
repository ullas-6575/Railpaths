<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$app->make(Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables::class)->bootstrap();
$view = $app->make('view');
echo $view->make('auth.login')->render();
