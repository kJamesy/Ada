<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', ['as' => 'guest.home', function () { return view('guest.home'); }]);
Route::get('/home', function () { return redirect(route('guest.home')); });

Route::get('lab', function() {

//	dd(\App\MailingList::whereIn('id', [3])->update(['is_deleted' => 0, 'updated_at' => \Carbon\Carbon::now()->toDateTimeString()]));
});

/**
 * Admin Routes
 */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::group(['namespace' => 'Auth'], function() {
        Route::get('register', ['as' => 'admin.auth.show_registration', 'uses' => 'RegisterController@showRegistrationForm']);
        Route::post('register', ['as' => 'admin.auth.store_registration', 'uses' => 'RegisterController@register']);
        Route::get('login', ['as' => 'admin.auth.show_login', 'uses' => 'LoginController@showLoginForm']);
        Route::post('login', ['as' => 'admin.auth.process_login', 'uses' => 'LoginController@login']);
        Route::get('password/reset', ['as' => 'admin.auth.show_password_reset', 'uses' => 'ForgotPasswordController@showLinkRequestForm']);
        Route::post('password/email', ['as' => 'admin.auth.send_password_reset_email', 'uses' => 'ForgotPasswordController@sendResetLinkEmail']);
        Route::get('password/reset/{token}', ['as' => 'admin.auth.show_password_reset_form', 'uses' => 'ResetPasswordController@showResetForm']);
        Route::post('password/reset', ['as' => 'admin.auth.process_password_reset_form', 'uses' => 'ResetPasswordController@reset']);
        Route::post('logout', ['as' => 'admin.auth.post_logout', 'uses' => 'LoginController@logout']);
        Route::get('logout', ['as' => 'admin.auth.get_logout', 'uses' => 'LoginController@logout']);
    });

    Route::group(['middleware' => ['auth']], function() {
        Route::group(['middleware' => ['active']], function() {
            Route::get('/', ['as' => 'admin.home', 'uses' => 'AdminController@index']);

            if ( ! request()->ajax() ) {
                Route::get('settings/{vue?}', 'AdminController@index');
                Route::get('users/export', 'UserController@export');
                Route::get('users/{vue?}', 'UserController@index');
	            Route::get('mailing-lists/export', 'MailingListController@export');
	            Route::get('mailing-lists/{vue?}', 'MailingListController@index');
	            Route::get('subscribers/export', 'SubscriberController@export');
	            Route::get('subscribers/{vue?}', 'SubscriberController@index');
            }

            Route::resource('settings', 'AdminController');
            Route::put('users/{option}/quick-update', 'UserController@quickUpdate');
            Route::resource('users', 'UserController');
	        Route::put('mailing-lists/{option}/quick-update', 'MailingListController@quickUpdate');
	        Route::resource('mailing-lists', 'MailingListController');
	        Route::put('subscribers/{option}/quick-update', 'SubscriberController@quickUpdate');
	        Route::resource('subscribers', 'SubscriberController');
        });

        Route::get('inactive', ['as' => 'admin.inactive', 'middleware' => 'inactive', function () { return view('admin.inactive'); }]);
    });


    Route::get('login-helper', ['as' => 'login', function () { return redirect(route('admin.auth.show_login')); }]);
});
