<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([
    'middleware' => 'api',
    'prefix' => 'v1',
    'namespace' => 'API'
], function(){
    // Auth endpoint
    Route::group(['prefix' => 'auth'], function(){
        Route::post('login', 'AuthController@login');
        Route::get('profile', 'AuthController@profile');
        Route::put('change-password', 'AuthController@changePassword');
        Route::post('logout', 'AuthController@logout');
    });

    // Profile endpoint
    Route::group(['prefix' => 'profile'], function(){
        Route::put('update', 'ProfileController@update');
    });

    // API resource endpoint
    Route::apiResources([
        'app-type' => 'ApplicationTypeController'
    ]);
});
