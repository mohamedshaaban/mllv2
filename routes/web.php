<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', 'HomeController@index');
Route::get('/xero/auth/callback', 'HomeController@xaerauth');

//Route::get('pay/{url}', 'PaymentController@payment');
//Route::get('tapreturn/{url}', 'PaymentController@tapreturn');
//Route::get('tapinvoicereturn/{url}', 'PaymentController@tapinvoicereturn');

Route::get('/pay/{token}/{staus}/{payment}', 'PaymentController@payReturn')->name('paySuccess');
Route::get('/pay/invoice/{id}', 'PaymentController@payInvoice')->name('payInvoice');
Route::get('/share/order/{id}', 'OrdersController@shareOrder')->name('shareOrder');
Route::get('/copy/order/{id}', 'OrdersController@copyOrder')->name('copyOrder');
Route::get('/payxeroinvoice/{magiclink}', 'PaymentController@xeropdf')->name('xeropdf');
Route::post('/pay', 'PaymentController@makePayment')->name('makePayment');
Route::post('/orderpay', 'PaymentController@makeOrderPayment')->name('makeOrderPayment');
Route::get('/payorder/{url}', 'PaymentController@payOrderInvoice')->name('payOrder');

