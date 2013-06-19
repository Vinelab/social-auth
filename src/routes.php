<?php

Route::group(['prefix' => 'auth'], function(){

	Route::group(['prefix' => 'social'], function(){

		Route::get('{service}', 'Vinelab\Auth\Controllers\AuthenticationController@index');
		Route::get('{service}/callback', 'Vinelab\Auth\Controllers\AuthenticationController@callback');
	});
});

Route::group(['prefix'=>'user'], function(){

	Route::group(['prefix'=>'profile'], function(){
		Route::get('', 'Vinelab\Auth\Controllers\ProfileController@basic');
		Route::get('full', 'Vinelab\Auth\Controllers\ProfileController@full');
	});

});