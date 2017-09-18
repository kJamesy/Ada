<?php

namespace App\Http\Controllers\Admin;

use App\Campaign;
use App\Email;
use App\EmailSetting;
use App\MailingList;
use App\Permissions\UserPermissions;
use App\Settings\UserSettings;
use App\Subscriber;
use App\Template;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EmailController extends Controller
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
	 * EmailController constructor.
	 */
	public function __construct()
	{
		$this->redirect = route('admin.home');
		$this->rules = Email::$rules;
		$this->perPage = 25;
		$this->orderByFields = ['subject', 'sender', 'recipients_num', 'status', 'created_at', 'updated_at', 'sent_at'];
		$this->orderCriteria = ['asc', 'desc'];
		$this->settingsKey = 'emails';
		$this->policies = UserPermissions::getPolicies();
		$this->policyOwnerClass = Email::class;
		$this->permissionsKey = UserPermissions::getModelShortName($this->policyOwnerClass);
		$this->friendlyName = 'Email';
		$this->friendlyNamePlural = 'Emails';
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
				return view('admin.emails')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey]);
			}
			else {
				$settings = UserSettings::getSettings($user->id, $this->settingsKey, $orderBy, $order, $perPage, true);
				$userId = (int) $request->userId;
				$campaignId = (int) $request->campaignId;
				$status = (int) $request->drafts ? -2 : 2;
				$search = strtolower($request->search);

				$resources = $search
					? Email::getSearchResults($search, $userId, $campaignId, $status, $deleted, $settings["{$this->settingsKey}_per_page"] )
					: Email::getResources($userId, $campaignId, $status, [], $deleted, $settings["{$this->settingsKey}_order_by"], $settings["{$this->settingsKey}_order"],
						$settings["{$this->settingsKey}_per_page"] );

				$deletedNum = Email::getCount(1);
				$draftsNum = Email::getCount(0, -2);

				$campaign = $campaignId ? Campaign::findResource($campaignId) : null;
				$campaigns = Campaign::getAttachedResources();

				$user = $userId ? User::findResource($userId) : null;
				$users = User::getAttachedResources();

				if ( $resources->count() )
					return response()->json(compact('resources', 'deletedNum', 'draftsNum', 'campaign', 'campaigns', 'user', 'users'));
				else
					return response()->json(['error' => "No $this->friendlyNamePlural found", 'deletedNum' => $deletedNum, 'draftsNum' => $draftsNum], 404);
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
			$subscribers = Subscriber::getAttachableResources();
			$mailing_lists = MailingList::getAttachedResources();
			$campaigns = Campaign::getAttachableResources();
			$templates = Template::getAttachableResources();
			$sender_email = EmailSetting::getSenderEmail();
			$sender_name = EmailSetting::getSenderName();
			$reply_to_email = EmailSetting::getReplyToEmail();

			return response()->json(compact('subscribers', 'mailing_lists', 'campaigns', 'templates', 'sender_email', 'sender_name', 'reply_to_email'));
		}

		return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
	}

	/**
	 * Store a newly created resource in storage.
	 * @param Request $request
	 * @return Email|\Illuminate\Http\JsonResponse
	 */
	public function store(Request $request)
	{
		if ( $request->user()->can('create', $this->policyOwnerClass) ) {

			$rules = $this->rules;

			if ( $request->is_draft ) {
				$rules = array_diff_key( $rules, [ 'sender_name'    => '',
				                                   'sender_email'   => '',
				                                   'reply_to_email' => '',
				                                   'subscribers'  => '',
				                                   'mailing_lists'    => ''
				] );
			}

			$this->validate($request, $rules);

			if ( $request->editing && $request->id ) { //Composing an email from an existing one
				$oldEmail = Email::findResource( (int) $request->id );

				if (  $oldEmail && $oldEmail->status === - 2 ) { //Old one was a draft
					if ( $request->is_draft ) //Modifying the draft
						return $this->update( $request, $request->id );
					else //Sending the draft
						$oldEmail->delete();
				}
			}

			$send_at = Carbon::now();

			if ( $rawTime = $request->send_at ) {
				$userInputDt = Carbon::createFromFormat( 'Y-m-d H:i', $rawTime, 'UTC');

				if ( ! $userInputDt->isPast() )
					$send_at = $userInputDt;
			}

			$user = $request->user();

			$resource = new Email();
			$resource->user_id = $user->id;
			$resource->campaign_id = (int) $request->campaign;

			if ( ! $request->is_draft ) {
				$resource->sender         = trim( $request->sender_name ) . "<" . trim( $request->sender_email ) . ">";
				$resource->reply_to_email = trim( $request->reply_to_email );
			}

			$resource->subject = trim($request->subject);
			$resource->content = trim($request->get('content'));
			$resource->status = $request->is_draft ? -2 : -1;
			$resource->save();

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
		$resource = Email::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$resource->url = route('emails.display', ['id' => $resource->id]);
			$resource->pdf = "{$this->pdfIt}?url={$resource->url}&pdfName=" . str_slug($resource->subject);

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
		if ( $resource = Email::findResource( (int) $id) )
			echo $resource->content;
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
		$resource = Email::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('update', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$subscribers = Subscriber::getAttachableResources();
			$mailing_lists = MailingList::getAttachedResources();
			$campaigns = Campaign::getAttachableResources();
			$templates = Template::getAttachableResources();
			$sender_email = EmailSetting::getSenderEmail();
			$sender_name = EmailSetting::getSenderName();
			$reply_to_email = EmailSetting::getReplyToEmail();

			return response()->json(compact('resource', 'subscribers', 'mailing_lists', 'campaigns', 'templates', 'sender_email', 'sender_name', 'reply_to_email'));
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
		$resource = Email::findResource( (int) $id );

		if ( $resource ) {
			$user = $request->user();

			$resource->user_id = $user->id;
			$resource->campaign_id = (int) $request->campaign;
			$resource->subject = trim($request->subject);
			$resource->content = trim($request->get('content'));
			$resource->save();

			$resource->just_updated = true;

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
		$resource = Email::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $currentUser->can('delete', $this->policyOwnerClass) ) {
			if ( ! $resource )
				return response()->json(['error' => "$this->friendlyName does not exist"], 404);

			$suffix = 'permanently deleted';

			if ( $resource->status === -2 ) //Draft
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


}
