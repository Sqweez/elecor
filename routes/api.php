<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Clients Routes

Route::get('test', 'Api\ClientController@test');


Route::prefix('clients')->group(function () {
    Route::get('types', 'Api\ClientController@getTypes');
    Route::post('create_clients', 'Api\ClientController@createClients');
    Route::put('push/{client}', 'Api\ClientController@push');
    Route::put('pushes', 'Api\ClientController@pushes');
    Route::patch('update/{client}', 'Api\ClientController@updateClient');
    Route::post('push/{client}', 'Api\ClientController@push');
    Route::post('pushes', 'Api\ClientController@pushes');
    Route::get('parseClients', 'Api\ClientController@parseClients');
    //@TODO УБРАТЬ!
    Route::get('clear', 'Api\ClientController@clear');
});
Route::resource('clients', 'Api\ClientController');

// Clients Routes End

Route::post('/upload', 'Service\ImageController@upload');
Route::post('/delete', 'Service\ImageController@delete');
Route::post('/parse_clients', 'Service\ExcelController@parseClients');
Route::post('/parse_balance', 'Service\ExcelController@parseBalance');

Route::post('/connections/find', 'Api\ConnectionController@findAccount');

Route::post('/connections/balances', 'Api\ConnectionController@addBalances');


// Service Routes

Route::prefix('services')->group(function () {
    Route::get('temp/{service}', 'Api\ServiceController@getTempServices');
});
Route::resource('services', 'Api\ServiceController');

Route::prefix('connections')->group(function () {
    Route::get('duplicates', 'Api\ConnectionController@getDuplicate');
    Route::patch('disconnect/{connection}', 'Api\ConnectionController@disconnect');
    Route::patch('connect/{connection}', 'Api\ConnectionController@connect');
    Route::post('balance/{connection}', 'Api\ConnectionController@addBalance');
    Route::post('sale', 'Api\ConnectionController@makeSale');
    Route::get('history/{connection}', 'Api\ConnectionController@history');
});

Route::resource('connections', 'Api\ConnectionController');
Route::resource('stocks', 'Api\StockController');
Route::resource('orders', 'Api\OrderController');
Route::resource('feedback', 'Api\FeedbackController');

Route::prefix('mobile')->group(function () {
    Route::post('auth', 'Api\MobileController@sms');
    Route::get('client/{client}', 'Api\MobileController@getClientData');
    Route::get('messages/{client}', 'Api\MobileController@messages');
    Route::patch('messages/{message}', 'Api\MobileController@updateMessage');
    Route::patch('contacts/{contact}', 'Api\MobileController@updateContacts');
    Route::get('contacts', 'Api\MobileController@getContacts');
    Route::get('services', 'Api\MobileController@getServices');
    Route::post('services', 'Api\MobileController@createService');
    Route::delete('services/{service}', 'Api\MobileController@deleteService');
    Route::patch('services/{service}', 'Api\MobileController@editService');
});


