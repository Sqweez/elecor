<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DebugApi;

Route::middleware([DebugApi::class])->group(function () {
    Route::post('auth', 'Api\UserController@auth');
    Route::get('export/clients', 'Api\ExportController@exportClients');
    Route::get('export/debts', 'Api\ExportController@exportDebts');

    Route::middleware(['check_token'])->group(function () {
        // Ежемесячное списание с баланса
        Route::get('test', 'CronController@monthlyBalanceChange');
        // Поздравление с днем рождения крон
        Route::get('birthday', 'CronController@dailyBirthday');

        Route::resource('mailing_history', 'Api\MailingHistoryController');
        Route::resource('mailing_templates', 'Api\MailingTemplateController');
        Route::post('mailing', 'Api\MailingController@mailing');
        Route::resource('company', 'Api\CompanyController');

        Route::prefix('clients')->group(function () {
            Route::get('debt', 'Api\ClientController@getDebt');
            Route::get('types', 'Api\ClientController@getTypes');
            Route::post('create_clients', 'Api\ClientController@createClients');
            Route::put('push/{client}', 'Api\ClientController@push');
            Route::patch('update/{client}', 'Api\ClientController@updateClient');
            Route::post('push', 'Api\ClientController@push');
            Route::delete('transaction/{transaction}', 'Api\ClientController@deleteTransaction');
        });

        Route::get('fields', 'Api\FieldController@index');
        Route::post('fields', 'Api\FieldController@create');
        Route::patch('fields/{field}', 'Api\FieldController@change');

        Route::delete('fields/{field}', 'Api\FieldController@destroy');


        Route::resource('clients', 'Api\ClientController');

        Route::resource('users', 'Api\UserController');
        Route::get('roles', 'Api\UserController@getRoles');
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

        Route::get('feedbacks', 'Api\FeedbackController@count');

        Route::resource('connections', 'Api\ConnectionController');
        Route::resource('stocks', 'Api\StockController');
        Route::resource('orders', 'Api\OrderController');
        Route::resource('feedback', 'Api\FeedbackController');

        Route::resource('stats', 'Api\StatController');

        Route::prefix('mobile')->group(function () {
            Route::post('auth', 'Api\MobileController@sms');
            Route::delete('message/{message}', 'Api\MobileController@deleteMessage');
            Route::get('client/{client}', 'Api\MobileController@getClientData');
            Route::get('messages/{client}', 'Api\MobileController@messages');
            Route::patch('messages/{message}', 'Api\MobileController@updateMessage');
            Route::patch('contacts/{contact}', 'Api\MobileController@updateContacts');
            Route::get('contacts', 'Api\MobileController@getContacts');
            Route::get('services', 'Api\MobileController@getServices');
            Route::post('services', 'Api\MobileController@createService');
            Route::delete('services/{service}', 'Api\MobileController@deleteService');
            Route::patch('services/{service}', 'Api\MobileController@editService');
            Route::post('pay', 'Api\MobileController@pay');
            Route::get('welcome/{client}', 'Api\MobileController@welcome');
        });

        // v2 Роуты
        Route::prefix('v2')->group(function () {
            // URL для реферральной системы Elecor
            Route::prefix('referral')->group(function () {
                // Получение настроек реферальной системы
                Route::get('settings', 'Api\ReferralController@getSettings');
                // Обновление настроек реферальной системы
                Route::patch('settings', 'Api\ReferralController@updateSettings');
                // Проверка реферала, передается GET-параметром ref=[ref], зашифрованный или нет будет зависить от настроек
                Route::get('validation', 'Api\ReferralController@validateReferral');
                // Получаем откуда бы ни было реферальную ссылку, отправив
                Route::get('{client_id}/url', 'Api\ReferralController@getReferralUrl');
                // Проверяем промокод с сайта на верность, передаем GET-параметром promocode=[promocode],
                Route::get('coupon', 'Api\ReferralController@validateCoupon');
                // Для получения текущих бонусов у клиента
                // В случае если type=elecor.kz ищем по лиц счету
                // В остальных других - по ид клиента
                Route::get('bonuses/{client}', 'Api\ReferralController@getBonuses');
                // Начисление бонусов за успешное подключение реферального клиента
                Route::post('bonuses/credit', 'Api\ReferralController@creditBonuses');
                // Списание бонусов, за оплату ими, обналичивание и прочее.
                Route::post('bonuses/debit', 'Api\ReferralController@debitBonuses');
                // Генерация QR-кода клиента
                Route::get('{client}/qr', 'Api\ReferralController@getQRCode');
            });
        });

    });
});








