<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Permissions\UserPermissions;
use App\Settings\UserSettings;
use App\Template;
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

	/**
	 * CampaignController constructor.
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
				return view('admin.templates')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey]);
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
