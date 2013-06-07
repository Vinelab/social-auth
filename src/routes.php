<?php

Route::group(['prefix' => 'auth'], function(){

	Route::group(['prefix' => 'social'], function(){

		Route::get('{service}', function($service){

		});

	});
});