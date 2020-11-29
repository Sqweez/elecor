<?php

use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    $clients = \App\Http\Controllers\Service\DebtService::getDebts(request());
    return view('test', [
        'clients' => $clients
    ]);
});
Route::get('/{any}', 'VueController@index')->where('any', '.*');


