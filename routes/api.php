<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('sparkpost/webhooks', function (Request $request) {

	$sparky = new \App\SparkyResponse();
	$sparky->body = json_encode($request->all());
	$sparky->save();

	dispatch(new \App\Jobs\HandleSparkyResponse($sparky));

	return response()->json(['message' => 'We\'ll take it from here, thank you.'], 200);
});

Route::group(['namespace' => 'API'], function() {
	Route::group(['prefix' => 'v1'], function() {
		Route::get('get-mailing-lists', 'VersionOneController@getMailingLists');
		Route::any('is-subscribed/{email}', 'VersionOneController@isSubscribed');
		Route::post('subscribe', 'VersionOneController@subscribe');
	});
});