<?php

namespace App\Http\Controllers\Guest;

use App\Email;
use App\Helpers\EmailVariables;
use App\Helpers\Hashids;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailController extends Controller
{

	/**
	 * Display resource content
	 * @param $id
	 * @param Request $request
	 *
	 * @return mixed|string
	 */
	public function display($id, Request $request)
	{
		$id = (int) Hashids::decode($id);

		if ( $resource = Email::findResource($id) ) {
			$content = $resource->content;

			$decodedSubscriberId = request()->has('unique') ? (int) Hashids::decode(request()->get('unique')) : null;
			$subscriber = $decodedSubscriberId ? Subscriber::findResource($decodedSubscriberId) : null;

			if ( $decodedSubscriberId && $subscriber ) {
				$encodedEmailId = Hashids::encode($id);

				$unsubscribeUrl = route('subscriber.unsubscribe');
				$viewInBrowserUrl = route('guest-emails.display', ['id' => $encodedEmailId]);
				$reviewYourPreferencesUrl = route('subscriber.review');

				$substitutionVariables = EmailVariables::getSubstitutionVariables();

				return EmailVariables::replaceSubstitutionVariablesForBrowser($substitutionVariables, $content, $unsubscribeUrl, $viewInBrowserUrl, $reviewYourPreferencesUrl, $subscriber);
			}

			return $content;
		}

		return app()->abort(404);
	}
}
