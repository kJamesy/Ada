<?php

namespace App\Http\Controllers\Guest;

use App\EmailContent;
use App\Helpers\Content;
use App\Helpers\Hashids;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailContentController extends Controller
{
	/**
	 * Display resource content
	 * @param $id
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
	 */
	public function display($id, Request $request)
	{
		$id = (int) Hashids::decode($id);

		if ( $resource = EmailContent::findResource($id) ) {
			$replaced = new Content($resource->content);
			$content = $replaced->setH2sIdAttribute();
			$menu = $replaced->getAnchorsMenu();

			return view('guest.view-content', compact('resource', 'content', 'menu'));
		}

		return app()->abort(404);
	}

}
