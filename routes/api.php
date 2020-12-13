<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\DebugApi;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\SyncController;

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
            Route::get('genders', 'Api\ClientController@getGenders');
            Route::get('languages', 'Api\ClientController@getLanguages');
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
            // URL для мобильного приложения

            Route::prefix('mobile')->group(function () {
                Route::post('auth', 'Api\v2\MobileController@sms');
                Route::delete('message/{message}', 'Api\v2\MobileController@deleteMessage');
                Route::get('client/{client}', 'Api\v2\MobileController@getClientData');
                Route::get('messages/{client}', 'Api\v2\MobileController@messages');
                Route::patch('messages/{message}', 'Api\v2\MobileController@updateMessage');
                Route::patch('contacts/{contact}', 'Api\v2\MobileController@updateContacts');
                Route::get('contacts', 'Api\v2\MobileController@getContacts');
                Route::get('services', 'Api\MobileController@getServices');
                Route::post('services', 'Api\v2\MobileController@createService');
                Route::delete('services/{service}', 'Api\v2\MobileController@deleteService');
                Route::patch('services/{service}', 'Api\v2\MobileController@editService');
                Route::post('pay', 'Api\v2\MobileController@pay');
                Route::get('welcome/{client}', 'Api\v2\MobileController@welcome');
            });

            // URL для реферральной системы Elecor
            Route::prefix('referral')->group(function () {
                // Получение настроек реферальной системы
                Route::get('settings', [ReferralController::class, 'getSettings']);
                // Обновление настроек реферальной системы
                Route::patch('settings', [ReferralController::class, 'updateSettings']);
                // Проверка реферала, передается GET-параметром ref=[ref], зашифрованный или нет будет зависить от настроек
                Route::get('validation', [ReferralController::class, 'validateReferral']);
                // Получаем откуда бы ни было реферальную ссылку, отправив
                Route::get('{client_id}/url', [ReferralController::class, 'getReferralUrl']);
                // Получаем откуда бы ни было сообщение с реф ссылкой, отправив
                Route::get('{client_id}/message', [ReferralController::class, 'getReferralMessage']);
                // Проверяем промокод с сайта на верность, передаем GET-параметром promocode=[promocode],
                Route::get('coupon', [ReferralController::class, 'validateCoupon']);
                // Для получения текущих бонусов у клиента
                // В случае если type=elecor.kz ищем по лиц счету
                // В остальных других - по ид клиента
                // Обязательно передаем type |elecor.web|elecor.admin|elecor.mobile|
                Route::get('{client}/bonuses', [ReferralController::class, 'getBonuses']);
                // Начисление бонусов за успешное подключение реферального клиента
                Route::match(['get', 'post'], 'bonuses/credit', [ReferralController::class, 'creditBonuses']);
                // Списание бонусов, за оплату ими, обналичивание и прочее.
                Route::match(['get','post'], 'bonuses/debit', [ReferralController::class, 'debitBonuses']);
                // Общий метод для списания и пополнения бонусов
                Route::post('bonuses', [ReferralController::class, 'storeBonuses']);
                // Генерация QR-кода клиента
                Route::get('{client}/qr', [ReferralController::class, 'getQRCode']);
                // Типы операций с бонусами
                Route::get('operations/types', [ReferralController::class, 'getOperationTypes']);
            });

            Route::prefix('sync')->group(function () {
                Route::get('db', [SyncController::class, 'syncDb']);
                Route::get('url', [SyncController::class, 'getUrl']);
            });
        });

    });
});








