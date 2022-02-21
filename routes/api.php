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

$router->post('login', 'AuthController@login');
$router->post('users', 'UserController@create');

Route::group(['middleware' => 'auth:api'], function() use($router) {
    $router->post('logout', 'AuthController@logout');
    $router->post('refresh', 'AuthController@refresh');
    $router->post('me', 'AuthController@me');

    $router->get('users', 'UserController@list')
        ->get('users/{id}', 'UserController@find')
        ->put('users/{id}', 'UserController@update')
        ->delete('users/{id}', 'UserController@delete');

    $router->get('services/', 'ServiceController@list')
        ->get('services/{id}', 'ServiceController@find')
        ->post('services/', 'ServiceController@create')
        ->put('services/{id}', 'ServiceController@update')
        ->delete('services/{id}', 'ServiceController@delete');

    $router->get('contracts/', 'ContractController@list')
        ->get('contracts/{id}', 'ContractController@find')
        ->post('contracts/', 'ContractController@create')
        ->put('contracts/{id}', 'ContractController@update')
        ->delete('contracts/{id}', 'ContractController@delete');
});



