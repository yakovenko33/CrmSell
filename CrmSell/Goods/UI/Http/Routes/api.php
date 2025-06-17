<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    Route::prefix('api/v1')
        ->namespace("CrmSell\Goods\UI\Http\Controllers")
        ->group(function () {
            Route::get('/goods', 'GoodsController@getList');
            Route::get('/goods/{id}', 'GoodsController@getGoodsById')->whereUuid('id');
            Route::post('/goods', 'GoodsController@create');
            Route::put('/goods', 'GoodsController@update');
            Route::get('/goods/vendor_code/{value}', 'GoodsController@getListByVendorCode');
            Route::get('/goods/goods_name/{value}', 'GoodsController@getListByGoodsName');
        });
});
