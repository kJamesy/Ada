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
Route::redirect('/home', route('guest.home'));


Route::group(['prefix' => 'lab'], function() {
	Route::get('/', function() {

	});

	Route::get('worker', function() {
		return exec("php " . base_path() . "/artisan supervise:queue-worker");
//		return \Illuminate\Support\Facades\Artisan::call('supervise:queue-worker');
	});
});

/**
 * Admin Routes
 */
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
	Route::group(['namespace' => 'Auth'], function() {
		if ( config('newsletter.allow_registration') ) {
			Route::get('register', ['as' => 'admin.auth.show_registration', 'uses' => 'RegisterController@showRegistrationForm']);
			Route::post('register', ['as' => 'admin.auth.store_registration', 'uses' => 'RegisterController@register']);
		}
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
			Route::get('/', ['as' => 'admin.home', 'uses' => 'DashboardController@index']);

			if ( ! request()->ajax() ) {
				Route::get('dashboard/{vue?}', 'DashboardController@index');
				Route::get('profile/{vue?}', 'ProfileController@index');
				Route::get('users/export', 'UserController@export');
				Route::get('users/{vue?}', 'UserController@index');
				Route::get('mailing-lists/export', 'MailingListController@export');
				Route::get('mailing-lists/{vue?}', 'MailingListController@index');
				Route::get('subscribers/export', 'SubscriberController@export');
				Route::get('subscribers/{vue?}', 'SubscriberController@index');
				Route::get('campaigns/export', 'CampaignController@export');
				Route::get('campaigns/{vue?}', 'CampaignController@index');
				Route::get('templates/export', 'TemplateController@export');
				Route::get('templates/{vue?}', 'TemplateController@index');
				Route::get('email-settings/export', 'EmailSettingController@export');
				Route::get('email-settings/{vue?}', 'EmailSettingController@index');
				Route::get('emails/export', 'EmailController@export');
				Route::get('emails/{vue?}', 'EmailController@index');
				Route::get('email-contents/{vue?}', 'EmailContentController@index');
				Route::get('user-guides/{vue?}', 'UserGuideController@index');
				Route::get('developer-guides/{vue?}', 'DeveloperGuideController@index');
			}

			Route::resource('dashboard', 'DashboardController');
			Route::resource('profile', 'ProfileController');
			Route::put('users/{option}/quick-update', 'UserController@quickUpdate');
			Route::resource('users', 'UserController');
			Route::put('mailing-lists/{option}/quick-update', 'MailingListController@quickUpdate');
			Route::resource('mailing-lists', 'MailingListController');
			Route::put('subscribers/{option}/quick-update', 'SubscriberController@quickUpdate');
			Route::resource('subscribers', 'SubscriberController');
			Route::put('campaigns/{option}/quick-update', 'CampaignController@quickUpdate');
			Route::resource('campaigns', 'CampaignController');
			Route::put('templates/{option}/quick-update', 'TemplateController@quickUpdate');
			Route::resource('templates', 'TemplateController');
			Route::put('email-settings/{option}/quick-update', 'EmailSettingController@quickUpdate');
			Route::resource('email-settings', 'EmailSettingController');
			Route::put('emails/{option}/quick-update', 'EmailController@quickUpdate');
			Route::get('emails/{id}/recipients', 'EmailController@getRecipients');
			Route::get('emails/{id}/general-stats', 'EmailController@getGeneralStats');
			Route::get('emails/{id}/opens-stats', 'EmailController@getOpensStats');
			Route::get('emails/{id}/clicks-stats', 'EmailController@getClicksStats');
			Route::get('emails/{id}/failures-stats', 'EmailController@getFailuresStats');
			Route::resource('emails', 'EmailController');
			Route::put('email-contents/{option}/quick-update', 'EmailContentController@quickUpdate');
			Route::resource('email-contents', 'EmailContentController');
			Route::put('user-guides/{option}/quick-update', 'UserGuideController@quickUpdate');
			Route::resource('user-guides', 'UserGuideController');
			Route::put('developer-guides/{option}/quick-update', 'DeveloperGuideController@quickUpdate');
			Route::resource('developer-guides', 'DeveloperGuideController');
		});

		Route::get('inactive', ['as' => 'admin.inactive', 'middleware' => 'inactive', function () { return view('admin.inactive'); }]);
	});

	Route::get('login-helper', ['as' => 'login', function () { return redirect(route('admin.auth.show_login')); }]);
});

Route::group(['prefix' => 'guest', 'namespace' => 'Guest'], function() {
	Route::get('templates/{id}/display', ['as' => 'guest-templates.display', 'uses' => 'TemplateController@display']);
	Route::get('emails/{id}/display', ['as' => 'guest-emails.display', 'uses' => 'EmailController@display']);
	Route::get('email-contents/{id}/display', ['as' => 'guest-email-contents.display', 'uses' => 'EmailContentController@display']);
	Route::get('user-guides', ['as' => 'guest-user-guides.index', 'uses' => 'UserGuideController@index']);
	Route::get('user-guides/search', ['as' => 'guest-user-guides.search', 'uses' => 'UserGuideController@search']);
	Route::get('user-guides/{slug}', ['as' => 'guest-user-guides.show', 'uses' => 'UserGuideController@show']);
	Route::get('developer-guides', ['as' => 'guest-developer-guides.index', 'uses' => 'DeveloperGuideController@index']);
	Route::get('developer-guides/search', ['as' => 'guest-developer-guides.search', 'uses' => 'DeveloperGuideController@search']);
	Route::get('developer-guides/{slug}', ['as' => 'guest-developer-guides.show', 'uses' => 'DeveloperGuideController@show']);
});

Route::group(['prefix' => 'subscriber', 'namespace' => 'Subscriber'], function() {
	Route::get('unsubscribe', ['as' => 'subscriber.unsubscribe', 'uses' => 'SubscriberController@unsubscribe']);
	Route::get('review-preferences', ['as' => 'subscriber.review', 'uses' => 'SubscriberController@reviewPreferences']);
	Route::post('update-preferences', ['as' => 'subscriber.update_preferences', 'uses' => 'SubscriberController@updatePreferences']);
});