<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->post('register',           'API\RegisterController@register');

Route::middleware('auth:api')->get('reports/status/{id}', 'API\ReportController@showByStatus');

Route::middleware('auth:api')->put('reports/status/{id}', 'API\ReportController@ChangeStatus');

Route::middleware('auth:api')->get('reports',             'API\ReportController@index');

Route::middleware('auth:api')->get('reports/{id}',        'API\ReportController@show');

Route::middleware('auth:api')->post('reports',            'API\ReportController@store');

Route::middleware('auth:api')->put('reports/{id}',        'API\ReportController@update');

Route::middleware('auth:api')->delete('reports/{id}',       'API\ReportController@destroy');

Route::middleware('auth:api')->get('reports/user/{id}',        'API\ReportController@showByUser');

Route::middleware('auth:api')->get('reports/user/{user_id}/{id}',        'API\ReportController@showByUserAndStatus');

Route::middleware('auth:api')->get('reports/users/all', 'API\ReportController@showGroupByUser');

Route::middleware('auth:api')->get('reports/main/all', 'API\ReportController@showMain');

Route::middleware('auth:api')->get('users',               'API\UserController@GetUsers');

Route::middleware('auth:api')->put('users/admin/{id}',    'API\UserController@SetAsAdmin');

Route::middleware('auth:api')->put('users/activate/{id}', 'API\UserController@DeActivateUser');

Route::middleware('auth:api')->get('categories',           'API\CategoryController@index');

Route::middleware('auth:api')->get('raw/{filename}', 'API\FileAccessController@show');

Route::middleware('auth:api')->get('login', 'API\LoginController@login');


