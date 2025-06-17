<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('api/v1')
        ->namespace("CrmSell\Providers\UI\Http\Controllers")
        ->group(function () {
            Route::get('/providers', 'ProvidersController@getList');
            Route::get('/provider/{id}', 'ProvidersController@getProviderById')->whereUuid('id');
            Route::post('/provider', 'ProvidersController@create');
            Route::put('/provider', 'ProvidersController@edit');
            Route::get('/providers/all', 'ProvidersController@getListAll');
        });
});
