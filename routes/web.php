<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', App\Http\Controllers\Welcome::class);

Route::group(['prefix' => 'directory'], function() {
    Route::get('/{id}', App\Http\Controllers\Directory\Crawl::class);
});

Route::get('test_site', App\Http\Controllers\TestSite::class);
