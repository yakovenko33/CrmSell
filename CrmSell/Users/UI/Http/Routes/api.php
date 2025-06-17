<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('api/v1/')
        ->group(function () {
            Route::get('/user', function (Request $request) {
                return $request->user();
            });
        });
});

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('api/v1')
        ->namespace("CrmSell\Users\UI\Http\Controllers")
        ->group(function () {
            Route::post('/user', 'UsersController@addUser');
            Route::put('/user', 'UsersController@updateUser');
            Route::get('/user', 'UsersController@getUser');
            Route::get('/users', 'UsersController@getList');
            Route::get('/users/all', 'UsersController@getListAll');
            Route::get('/roles', 'UsersController@getRoles');
            Route::get('/user/{id}', 'UsersController@getUserById')->whereUuid('id');
            Route::get('/user/roles/{id}', 'UsersController@getUserRolesId')->whereUuid('id');
            Route::post('/user/role/', 'UsersController@addRole');
            Route::post('/user/role/untie/', 'UsersController@untieRole');
        });
});
