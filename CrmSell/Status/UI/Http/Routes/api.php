<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('api/v1')
        ->namespace("CrmSell\Status\UI\Http\Controllers")
        ->group(function () {
            Route::get('/status', 'StatusController@getList');
            Route::get('/status/{type}/{id}', 'StatusController@getStatusById')
                ->whereAlpha('type')
                ->whereUuid('id');
            Route::post('/status', 'StatusController@create');
            Route::put('/status', 'StatusController@edit');
            Route::get('/status/all', 'StatusController@getListAll');
            Route::get('/defects/all', 'StatusController@getListAllDefect');
        });
});
