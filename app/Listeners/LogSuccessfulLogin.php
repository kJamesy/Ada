<?php

namespace App\Listeners;

use App\LoginActivity;
use Carbon\Carbon;
use Illuminate\Auth\Events\Login;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class LogSuccessfulLogin
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{

	}

	/**
	 * Handle the event.
	 *
	 * @param  Login  $event
	 * @return void
	 */
	public function handle(Login $event)
	{
		$user = $event->user;

		$login = new LoginActivity();
		$login->user_id = $user->id;
		$login->attempted_at = Carbon::now();
		$login->success = 1;
		$login->save();
	}
}
