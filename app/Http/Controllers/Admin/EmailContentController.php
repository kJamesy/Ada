<?php

namespace App\Http\Controllers\Admin;

use App\EmailContent;
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

			$slug = self::generateSlug(EmailContent::getExistingSlugs(), str_slug($request->title));

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


	/**
	 * Generate a unique slug
	 * @param $existingSlugs
	 * @param $proposedSlug
	 *
	 * @return mixed
	 */
	protected static function generateSlug($existingSlugs, $proposedSlug)
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

			return static::generateSlug($existingSlugs, $newSlug);
		}

		return $proposedSlug;
	}

}
