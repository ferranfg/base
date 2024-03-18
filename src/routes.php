<?php

use Ferranfg\Base\Clients\Unsplash;
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

if (config('feed.feeds.main.items')) Route::feeds('feed');

Route::group(['middleware' => 'web'], function ()
{
    Route::get('/_ah/{action}', '\Ferranfg\Base\Http\Controllers\SchedulerController@engine');
    Route::get('/scheduler/weekly', '\Ferranfg\Base\Http\Controllers\SchedulerController@weekly');
    Route::get('/scheduler/daily', '\Ferranfg\Base\Http\Controllers\SchedulerController@daily');

    Route::get('/newsletter', '\Ferranfg\Base\Http\Controllers\NewsletterController@index')->name('newsletter.index');
    Route::post('/newsletter/subscribe', '\Ferranfg\Base\Http\Controllers\NewsletterController@subscribe')->name('newsletter.subscribe');
    Route::get('/newsletter/unsubscribe/{token}', '\Ferranfg\Base\Http\Controllers\NewsletterController@unsubscribe')->name('newsletter.unsubscribe');

    Route::get('/cookies', '\Ferranfg\Base\Http\Controllers\LegalController@cookies')->name('cookies');
    Route::get('/privacy', '\Ferranfg\Base\Http\Controllers\LegalController@privacy')->name('privacy');
    Route::get('/terms', '\Ferranfg\Base\Http\Controllers\LegalController@terms')->name('terms');

    Route::get('/blog', '\Ferranfg\Base\Http\Controllers\BlogController@list')->name('blog.list');
    Route::get('/tag/{keyword}', '\Ferranfg\Base\Http\Controllers\BlogController@tag')->name('blog.tag');
    Route::get('/blog/{slug}', '\Ferranfg\Base\Http\Controllers\BlogController@post')->name('blog.post');
    Route::post('/blog/{slug}', '\Ferranfg\Base\Http\Controllers\BlogController@comment')->name('blog.comment');

    Route::get('/chat', '\Ferranfg\Base\Http\Controllers\ChatController@index');
    Route::post('/chat', '\Ferranfg\Base\Http\Controllers\ChatController@message');

    Route::get('/connect', '\Ferranfg\Base\Http\Controllers\ConnectController@facebook');
    Route::get('/connect/facebook', '\Ferranfg\Base\Http\Controllers\ConnectController@callbackFacebook');
    Route::get('/connect/instagram', '\Ferranfg\Base\Http\Controllers\ConnectController@instagram');
    Route::post('/connect/instagram', '\Ferranfg\Base\Http\Controllers\ConnectController@callbackInstagram');
    Route::get('/connect/return', '\Ferranfg\Base\Http\Controllers\ConnectController@return');
    Route::get('/connect/cancel', '\Ferranfg\Base\Http\Controllers\ConnectController@cancel');
    Route::post('/connect/upload', '\Ferranfg\Base\Http\Controllers\ConnectController@upload')
        ->middleware('auth:api')->withoutMiddleware('web');

    Route::get('/guides', '\Ferranfg\Base\Http\Controllers\GuidesController@index');
    Route::get('/guides/{id}', '\Ferranfg\Base\Http\Controllers\GuidesController@answer')->where('id', '[0-9]+');
    Route::get('/guides/{slug}', '\Ferranfg\Base\Http\Controllers\GuidesController@show');
    Route::post('/guides/{slug}', '\Ferranfg\Base\Http\Controllers\GuidesController@comment');

    Route::get('/html2img', '\Ferranfg\Base\Http\Controllers\Html2imgController@preview')->name('html2img.preview');
    Route::post('/html2img', '\Ferranfg\Base\Http\Controllers\Html2imgController@generate')->name('html2img.generate');

    Route::get('/notes/{slug?}', '\Ferranfg\Base\Http\Controllers\NoteController@index');

    Route::get('/shop', '\Ferranfg\Base\Http\Controllers\ShopController@list');
    Route::get('/shop/{slug}', '\Ferranfg\Base\Http\Controllers\ShopController@product');
    Route::post('/shop/{slug}', '\Ferranfg\Base\Http\Controllers\ShopController@add');
    Route::post('/shop/{slug}/review', '\Ferranfg\Base\Http\Controllers\ShopController@review');
    Route::get('/random', '\Ferranfg\Base\Http\Controllers\ShopController@random');

    Route::any('/cart', '\Ferranfg\Base\Http\Controllers\CheckoutController@cart');
    Route::get('/checkout', '\Ferranfg\Base\Http\Controllers\CheckoutController@redirect');
    Route::post('/invoice', '\Ferranfg\Base\Http\Controllers\CheckoutController@invoice');
    Route::get('/success', '\Ferranfg\Base\Http\Controllers\CheckoutController@success');
    Route::get('/cancel', '\Ferranfg\Base\Http\Controllers\CheckoutController@cancel');

    Route::get('/feed/merchant.xml', '\Ferranfg\Base\Http\Controllers\FeedController@merchant');

    Route::get('/unsplash/{method}/{param?}', function (Request $request, $method, $param = null)
    {
        return Unsplash::request($request, $method, $param);
    });
});

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