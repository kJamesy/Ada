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


Route::group(['prefix' => 'lab'], function() {
	Route::get('/', function() {

		try {
			$data = (new League\ISO3166\ISO3166)->alpha2('GB');

			var_dump($data['name']);

		}
		catch (\Exception $e) {
			var_dump($e);
		}


		$ua = 'Mozilla/5.0 (Linux; Android 8.0.0; Pixel Build/OPR3.170623.007; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/60.0.3112.116 Mobile 
		Safari/537.36';

		$ua2 = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 10.0; WOW64; Trident/7.0; .NET4.0C; .NET4.0E; .NET CLR 2.0.50727; .NET CLR 3.0.30729; .NET CLR 3.5.30729; Microsoft Outlook 16.0.7369; ms-office; MSOffice 16)';

		$dd = new \DeviceDetector\DeviceDetector($ua2);

		$dd->parse();
		$osName = $dd->getOs('name');
		$osVersion = $dd->getOs('version');
		$deviceName = $dd->getDeviceName();
		$browser = $dd->isBrowser();


		var_dump($dd->isMobile());
		echo "<br />";
		var_dump($deviceName);
		echo "<br />";
		var_dump($osName);
		echo "<br />";
		var_dump($osVersion);
		echo "<br />";
//
		$uaParser = \UAParser\Parser::create();
		$result = $uaParser->parse($ua2);

		var_dump($result->ua->family);
		echo "<br />";
		var_dump($result->ua->major);
	});

	Route::get('worker', function() {
		return exec("php " . base_path() . "/artisan supervise:queue-worker");
	});
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
	            Route::get('campaigns/export', 'CampaignController@export');
	            Route::get('campaigns/{vue?}', 'CampaignController@index');
	            Route::get('templates/export', 'TemplateController@export');
	            Route::get('templates/{vue?}', 'TemplateController@index');
	            Route::get('email-settings/export', 'EmailSettingController@export');
	            Route::get('email-settings/{vue?}', 'EmailSettingController@index');
	            Route::get('emails/export', 'EmailController@export');
	            Route::get('emails/{vue?}', 'EmailController@index');
            }

            Route::resource('settings', 'AdminController');
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
	        Route::resource('emails', 'EmailController');
        });

        Route::get('inactive', ['as' => 'admin.inactive', 'middleware' => 'inactive', function () { return view('admin.inactive'); }]);
    });

    Route::get('login-helper', ['as' => 'login', function () { return redirect(route('admin.auth.show_login')); }]);
});

Route::group(['prefix' => 'guest'], function() {
	Route::get('templates/{id}/display', ['as' => 'templates.display', 'uses' => 'Admin\\TemplateController@display']);
	Route::get('emails/{id}/display', ['as' => 'emails.display', 'uses' => 'Admin\\EmailController@display']);
});

Route::group(['prefix' => 'subscriber'], function() {
	Route::get('unsubscribe', ['as' => 'unsubscribe', function() {
		return 'Unsubscribed';
	}]);
});