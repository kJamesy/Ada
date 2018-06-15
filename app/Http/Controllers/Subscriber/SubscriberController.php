<?php

namespace App\Http\Controllers\Subscriber;

use App\Helpers\Hashids;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriberController extends Controller
{

	/**
	 * Unsubscribe subscriber
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function unsubscribe(Request $request)
    {
	    $subscriber = null;

	    if ( $id = request()->get('unique') ) {
		    if ( $subscriber = Subscriber::findResource( Hashids::decode( $id ) ) )
			    $subscriber = Subscriber::deactivate($subscriber);
	    }

	    return view('subscriber.unsubscribe', compact('subscriber'));
    }

	/**
	 * Display review-your-preferences form
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function reviewPreferences(Request $request)
    {
	    $subscriber = null;
	    $id = null;

	    if ( $id = request()->get('unique') )
		    $subscriber = Subscriber::findResource( Hashids::decode( $id ) );

	    return view('subscriber.review-preferences', compact('subscriber', 'id'));
    }

	/**
	 * Process review-your-preferences form
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function updatePreferences(Request $request)
	{
		$id = $request->unique ?: null;
		$resource = Subscriber::findResource( Hashids::decode($id) );

		if ( $resource ) {

			$rules = Subscriber::$rules;

			if ( strtolower($resource->email) === strtolower(trim($request->email)) )
				unset($rules['email']);

			$rules['consent'] = 'required';

			$this->validate($request, $rules);

			$resource->first_name = trim($request->first_name);
			$resource->last_name = trim($request->last_name);
			$resource->email = strtolower(trim($request->email));
			$resource->consent = (int) $request->consent ? 1 : 0;
			$resource->reviewed_at = Carbon::now();
			$resource->save();

			return redirect()->back()->withInput()->with(['success' => 'Great! Your preferences have been updated.']);
		}

		return redirect()->back()->withErrors('Subscriber not found.');
	}

}
