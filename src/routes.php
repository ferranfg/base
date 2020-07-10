<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Base Routes
|--------------------------------------------------------------------------
|
| Here is where you can register the base routes for your application.
| These routes are loaded by the BaseServiceProvider.
|
*/

Route::group(['prefix' => 'base/{locale}'], function ()
{
    // Posts
    Route::get('posts', '\Ferranfg\Base\Http\Controllers\PostController@all');
    Route::get('posts/{slug}', '\Ferranfg\Base\Http\Controllers\PostController@show');

    // Tags
    Route::get('tags', '\Ferranfg\Base\Http\Controllers\TagController@all');
    Route::get('tags/{slug}', '\Ferranfg\Base\Http\Controllers\TagController@show');
    Route::get('tags/{slug}/posts', '\Ferranfg\Base\Http\Controllers\TagController@posts');
});