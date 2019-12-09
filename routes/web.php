<?php

Route::get('/{any}', 'VueController@index')->where('any', '.*');

/*Route::get('/', 'Test\MainController@index');*/
