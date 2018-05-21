<?php

namespace App\Http\Controllers\API;

use App\MailingList;
use App\Subscriber;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class VersionOneController extends Controller
{

	/**
	 * Get a list of mailing lists that can be attached to subscribers
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getMailingLists()
	{
		$mailing_lists = MailingList::getAttachableResources();

		return count($mailing_lists) ? response()->json(compact('mailing_lists')) : response()->json(['error' => 'No mailing lists found'], 404);
	}

	/**
	 * Determine if supplied email is subscribed
	 * @param Request $request
	 * @param $email
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function isSubscribed(Request $request, $email)
	{
		$validator = Validator::make(compact('email'), ['email' => 'required|email']);

		if ( $validator->fails() )
			return response()->json(['error' => 'Please provide a valid email address'], 400);

		$subscriber = !! Subscriber::findResourceByEmail($email);
		$active = $subscriber ? !! $subscriber->active : false;
		$is_subscribed = !! $subscriber;

		return response()->json(compact('is_subscribed', 'active'));
	}

	/**
	 * Register supplied details as a new subscriber
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function subscribe(Request $request)
	{
		$rules = Subscriber::$rules;
		$rules['consent'] = 'required';

		$this->validate($request, $rules);

		try {
			$subscriber = new Subscriber();
			$subscriber->first_name = trim($request->first_name);
			$subscriber->last_name = trim($request->last_name);
			$subscriber->email = strtolower(trim($request->email));
			$subscriber->active = 1;
			$subscriber->consent = (int) $request->consent ? 1 : 0;
			$subscriber->reviewed_at = Carbon::now();
			$subscriber->save();

			if ( $request->has('mailing_lists') && is_array($request->mailing_lists) && count($request->mailing_lists) )
				$subscriber->mailing_lists()->attach($request->mailing_lists);

			return response()->json(['success' => true, 'message' => 'Subscriber successfully registered.', 'subscriber' => $subscriber]);
		}
		catch (\Exception $e) {
			return response()->json(['error' => $e->getMessage()], $e->getCode());
		}

	}

}
