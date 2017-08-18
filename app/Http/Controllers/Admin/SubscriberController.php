<?php

namespace App\Http\Controllers\Admin;

use App\MailingList;
use App\Permissions\UserPermissions;
use App\Settings\UserSettings;
use App\Subscriber;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscriberController extends Controller
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
	 * SubscriberController constructor.
	 */
	public function __construct()
	{
		$this->redirect = route('admin.home');
		$this->rules = Subscriber::$rules;
		$this->perPage = 25;
		$this->orderByFields = ['first_name', 'last_name', 'email', 'active', 'created_at', 'updated_at'];
		$this->orderCriteria = ['asc', 'desc'];
		$this->settingsKey = 'subscribers';
		$this->policies = UserPermissions::getPolicies();
		$this->policyOwnerClass = Subscriber::class;
		$this->permissionsKey = UserPermissions::getModelShortName($this->policyOwnerClass);
		$this->friendlyName = 'Subscriber';
		$this->friendlyNamePlural = 'Subscribers';
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
				return view('admin.subscribers')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey]);
			}
			else {
				$settings = UserSettings::getSettings($user->id, $this->settingsKey, $orderBy, $order, $perPage, true);
				$search = strtolower($request->search);

				$resources = $search
					? Subscriber::getSearchResults($search, $deleted, $settings["{$this->settingsKey}_per_page"])
					: Subscriber::getResources([], $deleted, $settings["{$this->settingsKey}_order_by"], $settings["{$this->settingsKey}_order"], $settings["{$this->settingsKey}_per_page"]);

				$deletedNum = Subscriber::getCount(1);

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
		    $mailing_lists = MailingList::getAttachableResources();
		    return response()->json(compact('mailing_lists'));
	    }

	    return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
    }

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Subscriber|\Illuminate\Http\JsonResponse
	 */
    public function store(Request $request)
    {
	    if ( $request->user()->can('create', $this->policyOwnerClass) ) {
		    $this->validate($request, $this->rules);

		    $resource = new Subscriber();
		    $resource->first_name = trim($request->first_name);
		    $resource->last_name = trim($request->last_name);
		    $resource->email = strtolower(trim($request->email));
		    $resource->active = $request->active ? 1 : 0;
		    $resource->save();

		    if ( $request->has('mailing_lists') && is_array($request->mailing_lists) && count($request->mailing_lists) )
			    $resource->mailing_lists()->attach($request->mailing_lists);

		    return $resource;
	    }

	    return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
    }

	/**
	 * Show specified resource.
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
    public function show($id, Request $request)
    {
	    $resource = Subscriber::findResource( (int) $id );
	    $currentUser = $request->user();

	    if ( $resource ) {
		    if ( ! $currentUser->can('read', $this->policyOwnerClass) )
			    return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

		    return response()->json(compact('resource'));
	    }

	    return response()->json(['error' => "$this->friendlyName does not exist"], 404);
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
