<?php

namespace App\Http\Controllers\Guest;

use App\Helpers\Hashids;
use App\Template;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TemplateController extends Controller
{

	/**
	 * Display resource content
	 * @param $id
	 * @param Request $request
	 */
	public function display($id, Request $request)
	{
		$id = (int) Hashids::decode($id);
		if ( $resource = Template::findResource($id) )
			return $resource->content;

		return app()->abort(404);
	}
}
