<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', function()
{
	return View::make('hello');
});

Route::group(array('prefix' => 'api'), function(){

	Route::post('gcm', 'GcmController@storeGcmID');

	Route::post('gcm/send', 'GcmController@sendMessage');

	Route::post('gcm/delete', 'GcmController@deleteGcmID');

	Route::post('gcm/update', 'GcmController@updateData');

});
