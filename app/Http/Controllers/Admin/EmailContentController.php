<?php

namespace App\Http\Controllers\Admin;

use App\EmailContent;
use App\Helpers\Content;
use App\Permissions\UserPermissions;
use App\Settings\UserSettings;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailContentController extends Controller
{
	protected $redirect;
	public $rules;
	public $perPage;
	public $orderByFields;
	public $orderCriteria;
	protected $settingsKey;
	protected $policies;
	protected $policyOwnerClass;
	protected $permissionsKey;
	protected $friendlyName;
	protected $friendlyNamePlural;

	/**
	 * CampaignController constructor.
	 */
	public function __construct()
	{
		$this->redirect = route('admin.home');
		$this->rules = EmailContent::$rules;
		$this->perPage = 25;
		$this->orderByFields = ['title', 'created_at', 'updated_at'];
		$this->orderCriteria = ['asc', 'desc'];
		$this->settingsKey = 'email_contents';
		$this->policies = UserPermissions::getPolicies();
		$this->policyOwnerClass = EmailContent::class;
		$this->permissionsKey = UserPermissions::getModelShortName($this->policyOwnerClass);
		$this->friendlyName = 'Email content';
		$this->friendlyNamePlural = 'Email contents';
	}

	/**
	 * Display a listing of the resource.
	 * @param Request $request
	 * @return $this|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
	 */
	public function index(Request $request)
	{
		if ( $request->user()->can('read', $this->policyOwnerClass) ) {
			$user = $request->user();

			$orderBy = in_array(strtolower($request->orderBy), $this->orderByFields) ? strtolower($request->orderBy) : $this->orderByFields[2];
			$order = in_array(strtolower($request->order), $this->orderCriteria) ? strtolower($request->order) : $this->orderCriteria[1];
			$perPage = (int) $request->perPage ?: $this->perPage;
			$deleted = (int) $request->trash;

			if ( ! $request->ajax() ) {
				return view('admin.email_contents')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey]);
			}
			else {
				$settings = UserSettings::getSettings($user->id, $this->settingsKey, $orderBy, $order, $perPage, true);
				$search = strtolower($request->search);

				$resources = $search
					? EmailContent::getSearchResults($search, $deleted, $settings["{$this->settingsKey}_per_page"])
					: EmailContent::getResources([], $deleted, $settings["{$this->settingsKey}_order_by"], $settings["{$this->settingsKey}_order"], $settings["{$this->settingsKey}_per_page"]);

				$deletedNum = EmailContent::getCount(1);

				if ( $resources->count() )
					return response()->json(compact('resources', 'deletedNum'));
				else
					return response()->json(['error' => "No $this->friendlyNamePlural found", 'deletedNum' => $deletedNum], 404);
			}
		}
		else {
			if ( $request->ajax() )
				return response()->json(['error' => 'You are not authorised to view this page.'], 403);
			else
				return redirect($this->redirect);
		}
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return EmailContent|\Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		if ( $request->user()->can('create', $this->policyOwnerClass) ) {
			$this->validate($request, $this->rules);

			$proposedSlug = $request->slug ? str_slug($request->slug) : str_slug($request->title);
			$slug = self::generateUniqueSlug(EmailContent::getExistingSlugs(), $proposedSlug);

			$resource = new EmailContent();
			$resource->user_id = $request->user()->id;
			$resource->title = trim($request->title);
			$resource->slug = $slug;
			$resource->content = trim($request->get('content'));
			$resource->save();

			return $resource;
		}

		return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
	}

	/**
	 * Show specified resource
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function show($id, Request $request)
	{
		$resource = EmailContent::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$resource->url = route('email-contents.display', ['id' => $resource->id]);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "$this->friendlyName does not exist"], 404);
	}

	/**
	 * Display resource content
	 * @param $id
	 */
	public function display($id)
	{
		if ( $resource = EmailContent::findResource( (int) $id) ) {
			$replaced = new Content($resource->content);
			$content = $replaced->setH2sIdAttribute();

			echo $content;
		}
		else
			echo "No $this->friendlyName found";
	}

	/**
	 * Show resource for editing
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($id, Request $request)
	{
		$resource = EmailContent::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('update', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "$this->friendlyName does not exist"], 404);
	}

	/**
	 * Update the specified resource
	 * @param Request $request
	 * @param $id
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function update(Request $request, $id)
	{
		$resource = EmailContent::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('update', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$this->validate($request, $this->rules);

			$proposedSlug = $request->slug ? str_slug($request->slug) : str_slug($request->title);
			$slug = ( $proposedSlug === $resource->slug ) ? $resource->slug : self::generateUniqueSlug(EmailContent::getExistingSlugs(), $proposedSlug);

			$resource->user_id = $request->user()->id;
			$resource->title = trim($request->title);
			$resource->slug = $slug;
			$resource->content = trim($request->get('content'));
			$resource->save();

			return $resource;
		}

		return response()->json(['error' => "$this->friendlyName does not exist"], 404);
	}

	/**
	 * Delete/destroy the specified resource
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id, Request $request)
	{
		$resource = EmailContent::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $currentUser->can('delete', $this->policyOwnerClass) ) {
			if ( ! $resource )
				return response()->json(['error' => "$this->friendlyName does not exist"], 404);

			$suffix = 'permanently deleted';

			if ( $request->permanent )
				$resource->delete();
			else {
				$resource->is_deleted = 1;
				$resource->save();
				$suffix = 'moved to trash';
			}

			return response()->json(['success' => "$this->friendlyName $suffix"]);
		}

		return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
	}

	/**
	 * Quickly update resources in bulk
	 * @param Request $request
	 * @param $update
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function quickUpdate(Request $request, $update)
	{
		$currentUser = $request->user();
		$resourceIds = $request->resources;

		if ( $currentUser->can('delete', $this->policyOwnerClass) ) {
			$selectedNum = count($resourceIds);

			if ( $selectedNum ) {
				try {
					$resources = EmailContent::getResources($resourceIds, -1)->pluck('id')->toArray();
					$successNum = 0;

					if ( $resources ) {
						switch ($update) {
							case 'delete':
								$successNum = EmailContent::doBulkActions($resources, 'delete');
								break;
							case 'restore':
								$successNum = EmailContent::doBulkActions($resources, 'restore');
								break;
							case 'destroy':
								$successNum = EmailContent::doBulkActions($resources, 'destroy');
								break;
						}

						$append = ( $selectedNum == $successNum ) ? '' : "Please note you do not have sufficient permissions to $update some $this->friendlyNamePlural.";
						$string = $successNum == 1 ? $this->friendlyName : $this->friendlyNamePlural;
						$successNum = $successNum ?: 'No';

						if ( $update == 'delete')
							$update = 'moved to trash';
						else if ( $update == 'destroy')
							$update = 'permanently deleted';
						else
							$update = "{$update}d";

						return response()->json(['success' => "$successNum $string $update. $append"]);
					}
					else
						return response()->json(['error' => "$this->friendlyNamePlural do not exist"], 404);
				}
				catch (\Exception $e) {
					return response()->json(['error' => 'A server error occurred.'], 500);
				}
			}
			else
				return response()->json(['error' => "No $this->friendlyNamePlural received"], 500);
		}

		return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
	}

	/**
	 * Generate a unique slug
	 * @param $existingSlugs
	 * @param $proposedSlug
	 *
	 * @return mixed
	 */
	protected static function generateUniqueSlug($existingSlugs, $proposedSlug)
	{
		$exists = false;

		if ( $existingSlugs ) {
			foreach ( $existingSlugs as $existingSlug ) {
				if ( strtolower( $proposedSlug ) === strtolower( $existingSlug ) ) {
					$exists = strtolower( $existingSlug );
					break;
				}
			}
		}

		if ( $exists ) {
			$parts = explode("-", $exists);
			$length = count($parts);
			$lastPart = $parts[$length-1];
			$newSlug = '';

			if( (int) $lastPart > 0 ) {
				$allOtherParts = $parts;
				unset($allOtherParts[$length-1]);
				$allOtherParts[] = (int) $lastPart + 1;

				foreach ($allOtherParts as $key=>$part) {
					if( $key !== $length )
						$newSlug .= $part . "-";
					else
						$newSlug .= $part;
				}
			}
			else
				$newSlug = $exists . '-1';

			return static::generateUniqueSlug($existingSlugs, $newSlug);
		}

		return $proposedSlug;
	}

}
