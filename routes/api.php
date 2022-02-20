<?php

/** @var Router $router */

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

use Illuminate\Support\Facades\Route;
use Laravel\Lumen\Routing\Router;

Route::group(['middleware' => 'auth:api'], function() use($router) {
    $router->group(['prefix' => 'auth'], function() use($router) {
        $router->post('login', 'AuthController@login');
        $router->post('logout', 'AuthController@logout');
        $router->post('refresh', 'AuthController@refresh');
        $router->post('me', 'AuthController@me');
    });
});

$router->group(['prefix' => 'users'], function() use($router) {
    $router->get('/', 'UserController@list')
        ->get('{id}', 'UserController@find')
        ->post('/', 'UserController@create')
        ->put('{id}', 'UserController@update')
        ->delete('{id}', 'UserController@delete');
});

$router->group(['prefix' => 'services'], function() use($router) {
    $router->get('/', 'ServiceController@list')
        ->get('{id}', 'ServiceController@find')
        ->post('/', 'ServiceController@create')
        ->put('{id}', 'ServiceController@update')
        ->delete('{id}', 'ServiceController@delete');
});

$router->group(['prefix' => 'contracts'], function() use($router) {
    $router->get('/', 'ContractController@list')
        ->get('{id}', 'ContractController@find')
        ->post('/', 'ContractController@create')
        ->put('{id}', 'ContractController@update')
        ->delete('{id}', 'ContractController@delete');
});

