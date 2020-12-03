<?php

use Illuminate\Support\Facades\Route;
Route::get('/test/{client}', 'VueController@test');
Route::get('/{any}', 'VueController@index')->where('any', '.*');


