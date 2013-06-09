<?php

Route::group(['prefix' => 'auth'], function(){

	Route::group(['prefix' => 'social'], function(){

		Route::get('{service}', 'Vinelab\Auth\Controllers\AuthenticationController@index');
		Route::get('{service}/callback', 'Vinelab\Auth\Controllers\AuthenticationController@callback');
	});
});