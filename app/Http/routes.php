<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

//$app->get('/', 'TestController@index');

/** @var \Dingo\Api\Routing\Router $api */
$api = app('Dingo\Api\Routing\Router');
$api->version('v1', function ($api) {
    /** @var \Dingo\Api\Routing\Router $api */
    $api->get('/', 'App\Http\Controllers\TestController@index');
});