<?php

Route::group(['middleware' => ['sessions']], function () {
    /*
    |----------------------------------
    | PRODUCTS
    |----------------------------------
    */
    Route::get('api/v1/shopping-cart/item/{instance?}',          'Syscover\ShoppingCart\Controllers\ShoppingCartController@index')->name('api.shopping_cart.item');
//    Route::get('api/v1/market/product/{id}/{lang}',                             'Syscover\Market\Controllers\ProductController@show')->name('api.market.show_product');
//    Route::post('api/v1/market/product/search',                                 'Syscover\Market\Controllers\ProductController@search')->name('api.market.search_product');
//    Route::post('api/v1/market/product',                                        'Syscover\Market\Controllers\ProductController@store')->name('api.market.store_product');
//    Route::put('api/v1/market/product/{id}/{lang}',                             'Syscover\Market\Controllers\ProductController@update')->name('api.market.update_product');
//    Route::delete('api/v1/market/product/{id}/{lang?}',                         'Syscover\Market\Controllers\ProductController@destroy')->name('api.market.destroy_product');
});