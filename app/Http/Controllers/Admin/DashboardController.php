<?php

namespace App\Http\Controllers\Admin;

use App\Email;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
	/**
	 * DashboardController constructor.
	 */
    public function __construct()
    {

    }

	/**
	 * Display a listing of the resource.
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		return view('admin.dashboard');
	}

	/**
	 * @param $id
	 * @return \Illuminate\Contracts\Auth\Authenticatable|\Illuminate\Http\JsonResponse|null
	 */
	public function show($id)
	{
		if ( $profile = Auth::user() ) {
			$lastEmail = Email::getLastSentEmailGeneralStats();
			$nextScheduledEmail = Email::findNextScheduledEmail();
			$todaysSubscribers = Subscriber::getSubscribersRegisteredToday();
			$subscribersCount = Subscriber::getCount();

			return response()->json(compact('profile', 'lastEmail', 'nextScheduledEmail', 'todaysSubscribers', 'subscribersCount'));
		}
		else
			return response()->json(['error' => 'User does not exist. Please login and try again'], 403);
	}

}
