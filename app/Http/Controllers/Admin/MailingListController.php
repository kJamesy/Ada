<?php

namespace App\Http\Controllers\Admin;

use App\Exporters\ResourceExporter;
use App\MailingList;
use App\Permissions\UserPermissions;
use App\Settings\UserSettings;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MailingListController extends Controller
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
	 * MailingListController constructor.
	 */
	public function __construct()
	{
		$this->redirect = route('admin.home');
		$this->rules = MailingList::$rules;
		$this->perPage = 25;
		$this->orderByFields = ['name', 'subscribers_count', 'created_at', 'updated_at'];
		$this->orderCriteria = ['asc', 'desc'];
		$this->settingsKey = 'mailing_lists';
		$this->policies = UserPermissions::getPolicies();
		$this->policyOwnerClass = MailingList::class;
		$this->permissionsKey = UserPermissions::getModelShortName($this->policyOwnerClass);
		$this->friendlyName = 'Mailing List';
		$this->friendlyNamePlural = 'Mailing Lists';
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

			$orderBy = in_array(strtolower($request->orderBy), $this->orderByFields) ? strtolower($request->orderBy) : $this->orderByFields[0];
			$order = in_array(strtolower($request->order), $this->orderCriteria) ? strtolower($request->order) : $this->orderCriteria[1];
			$perPage = (int) $request->perPage ?: $this->perPage;
			$deleted = (int) $request->trash;

			if ( ! $request->ajax() ) {
				return view('admin.mailing_lists')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey, 'activeGroup' => 'recipients']);
			}
			else {
				$settings = UserSettings::getSettings($user->id, $this->settingsKey, $orderBy, $order, $perPage, true);
				$search = strtolower($request->search);

				$resources = $search
					? MailingList::getSearchResults($search, $deleted, $settings["{$this->settingsKey}_per_page"])
					: MailingList::getResources([], $deleted, $settings["{$this->settingsKey}_order_by"], $settings["{$this->settingsKey}_order"], $settings["{$this->settingsKey}_per_page"]);

				$deletedNum = MailingList::getCount(1);

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
	 * @return MailingList|\Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		if ( $request->user()->can('create', $this->policyOwnerClass) ) {
			$this->validate($request, $this->rules);

			$resource = new MailingList();
			$resource->name = trim($request->name);
			$resource->description = trim($request->description);
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
		$resource = MailingList::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "$this->friendlyName does not exist"], 404);
	}

	/**
	 * Show resource for editing
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function edit($id, Request $request)
	{
		$resource = MailingList::findResource( (int) $id );
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
		$resource = MailingList::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('update', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$rules = $this->rules;

			if ( strtolower($resource->name) == strtolower(trim($request->name)) )
				$rules['name'] = str_replace("|unique:mailing_lists", '', $rules['name'] );

			$this->validate($request, $rules);

			$resource->name = trim($request->name);
			$resource->description = trim($request->description);
			$resource->save();

			return response()->json(compact('resource'));
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
		$resource = MailingList::findResource( (int) $id );
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
					$resources = MailingList::getResources($resourceIds, -1)->pluck('id')->toArray();
					$successNum = 0;

					if ( $resources ) {
						switch ($update) {
							case 'delete':
								$successNum = MailingList::doBulkActions($resources, 'delete');
								break;
							case 'restore':
								$successNum = MailingList::doBulkActions($resources, 'restore');
								break;
							case 'destroy':
								$successNum = MailingList::doBulkActions($resources, 'destroy');
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
	 * Export resources to Excel
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse|mixed
	 */
	public function export(Request $request)
	{
		if ( $request->user()->can('read', $this->policyOwnerClass) ) {
			$resourceIds = (array) $request->resourceIds;
			$fileName = '';

			$deleted = $request->has('trash') ? (int) $request->trash : -1;

			$resources = MailingList::getResources($resourceIds, $deleted);
			$fileName .= count($resourceIds) ? 'Specified-Items-' : 'All-Items-';
			$fileName .= Carbon::now()->toDateString();

			$exporter = new ResourceExporter($resources, $fileName);
			return $exporter->generateExcelExport('mailing_lists');
		}
		else
			return redirect()->back();
	}

}
