<?php

namespace App\Http\Controllers\Admin;

use App\Exporters\ResourceExporter;
use App\Helpers\Hashids;
use App\Http\Controllers\Controller;
use App\Permissions\UserPermissions;
use App\Settings\UserSettings;
use App\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TemplateController extends Controller
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
	protected $pdfIt;

	/**
	 * TemplateController constructor.
	 */
	public function __construct()
	{
		$this->redirect = route('admin.home');
		$this->rules = Template::$rules;
		$this->perPage = 25;
		$this->orderByFields = ['name', 'last_editor', 'created_at', 'updated_at'];
		$this->orderCriteria = ['asc', 'desc'];
		$this->settingsKey = 'templates';
		$this->policies = UserPermissions::getPolicies();
		$this->policyOwnerClass = Template::class;
		$this->permissionsKey = UserPermissions::getModelShortName($this->policyOwnerClass);
		$this->friendlyName = 'Template';
		$this->friendlyNamePlural = 'Templates';
		$this->pdfIt = 'http://pdf-it.dev.acw.website/please-and-thank-you';
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
				return view('admin.templates')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey, 'activeGroup' => 'content']);
			}
			else {
				$settings = UserSettings::getSettings($user->id, $this->settingsKey, $orderBy, $order, $perPage, true);
				$search = strtolower($request->search);

				$resources = $search
					? Template::getSearchResults($search, $deleted, $settings["{$this->settingsKey}_per_page"])
					: Template::getResources([], $deleted, $settings["{$this->settingsKey}_order_by"], $settings["{$this->settingsKey}_order"], $settings["{$this->settingsKey}_per_page"]);

				$deletedNum = Template::getCount(1);

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
	 * Show the form for creating a new resource.
	 * @return \Illuminate\Http\Response
	 */
	public function create(Request $request)
	{
		if ( $request->user()->can('create', $this->policyOwnerClass) ) {
			$templates = Template::getAttachableResources();

			return response()->json(compact('templates'));
		}

		return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Template|\Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		if ( $request->user()->can('create', $this->policyOwnerClass) ) {
			$this->validate($request, $this->rules);

			$user = $request->user();

			$resource = new Template();
			$resource->name = trim($request->name);
			$resource->description = trim($request->description);
			$resource->content = trim($request->get('content'));
			$resource->last_editor = "{$user->name}";
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
		$resource = Template::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$resource->url = route('guest-templates.display', ['id' => Hashids::encode($resource->id)]);
			$resource->pdf = "{$this->pdfIt}?url={$resource->url}&pdfName=" . str_slug($resource->name);

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
		$resource = Template::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('update', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$templates = Template::getAttachableResources();
			return response()->json(compact('resource', 'templates'));
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
		$resource = Template::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('update', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$rules = $this->rules;

			if ( strtolower($resource->name) === strtolower(trim($request->name)) )
				unset($rules['name']);

			$this->validate($request, $rules);

			$user = $request->user();

			$resource->name = trim($request->name);
			$resource->description = trim($request->description);
			$resource->last_editor = "{$user->name}";
			$resource->content = trim($request->get('content'));
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
		$resource = Template::findResource( (int) $id );
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
					$resources = Template::getResources($resourceIds, -1)->pluck('id')->toArray();
					$successNum = 0;

					if ( $resources ) {
						switch ($update) {
							case 'delete':
								$successNum = Template::doBulkActions($resources, 'delete');
								break;
							case 'restore':
								$successNum = Template::doBulkActions($resources, 'restore');
								break;
							case 'destroy':
								$successNum = Template::doBulkActions($resources, 'destroy');
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

			$resources = Template::getResources($resourceIds, $deleted);
			$fileName .= count($resourceIds) ? 'Specified-Items-' : 'All-Items-';
			$fileName .= Carbon::now()->toDateString();

			$exporter = new ResourceExporter($resources, $fileName);
			return $exporter->generateExcelExport('templates');
		}
		else
			return redirect()->back();
	}

}
