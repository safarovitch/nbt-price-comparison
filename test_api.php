<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $app->make(App\Http\Controllers\ExchangeRateController::class)->index($request);
echo $response->getContent();
