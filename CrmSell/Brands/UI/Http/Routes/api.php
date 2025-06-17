<?php

use Illuminate\Support\Facades\Route;


Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('api/v1')
        ->namespace("CrmSell\Brands\UI\Http\Controllers")
        ->group(function () {
            Route::get('/brands', 'GoodsController@getList');
            Route::get('/brand/{id}', 'GoodsController@getBrandById')->whereUuid('id');
            Route::post('/brand', 'GoodsController@create');
            Route::put('/brand', 'GoodsController@update');
            Route::get('/brands/{value}', 'BrandsController@getListByName');
        });
});
