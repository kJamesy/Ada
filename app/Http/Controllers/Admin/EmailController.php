<?php

namespace App\Http\Controllers\Admin;

use App\Campaign;
use App\Email;
use App\EmailSetting;
use App\Exporters\ResourceExporter;
use App\Helpers\Hashids;
use App\Jobs\SendNewsletter;
use App\MailingList;
use App\Permissions\UserPermissions;
use App\Rules\SendingDomain;
use App\Settings\UserSettings;
use App\Subscriber;
use App\Template;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
				return view('admin.emails')->with(['settingsKey' => $this->settingsKey, 'permissionsKey' => $this->permissionsKey, 'activeGroup' => 'content']);
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
				$users = User::getEmailAttachedResources();

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
			else
				$rules['sender_email'][] = new SendingDomain();

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

			$send_at = Carbon::now()->addMinute();

			if ( $rawTime = $request->send_at ) {
				$userInputDt = Carbon::createFromFormat( 'Y-m-d H:i', $rawTime, 'UTC');

				if ( ! $userInputDt->isPast() )
					$send_at = $userInputDt;
			}

			$user = $request->user();

			$resource = new Email();
			$resource->user_id = $user->id;
			$resource->campaign_id = (int) $request->campaign;
			$resource->subject = trim($request->subject);
			$resource->content = trim($request->get('content'));

			if ( ! $request->is_draft ) {
				$resource->sender = trim( $request->sender_name ) . "<" . trim( $request->sender_email ) . ">";
				$resource->reply_to_email = trim( $request->reply_to_email );
				$resource->sent_at = $send_at;
				$resource->status = -1;
			}

			if ( ! $request->is_draft ) {
				$subscribers = new Collection();

				if ( $request->subscribers )
					$subscribers = $subscribers->merge(Subscriber::getSpecifiedAttachableResources($request->subscribers));

				if ( $request->mailing_lists )
					$subscribers = $subscribers->merge(Subscriber::getAttachableResourcesBySpecifiedMLists($request->mailing_lists))->unique(); //unique just in case

				$sender = ['name' => trim( $request->sender_name ), 'email' => trim( $request->sender_email )];
				$subs_count = count($subscribers);

				if ( $subs_count ) {
					$resource->recipients_num = $subs_count;
					$resource->save();

					$job = ( new SendNewsletter( $resource, $subscribers, $sender ) )->delay( $send_at );
					dispatch( $job );
				}
			}
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

			$resource->url = route('emails.display', ['id' => Hashids::encode($resource->id)]);
			$resource->pdf = "{$this->pdfIt}?url={$resource->url}&pdfName=" . str_slug($resource->subject);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "$this->friendlyName does not exist"], 404);
	}

	/**
	 * Get email recipient stats
	 * @param $id
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getRecipients($id, Request $request)
	{
		$resource = Email::findResource( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			$types = ['injections', 'deliveries', 'opens', 'clicks', 'failures'];
			$orderByFields = [
				'injections' => ['first_name', 'injected_at', 'created_at', 'updated_at'],
				'deliveries' => ['first_name', 'delivered_at', 'created_at', 'updated_at'],
				'opens' => ['first_name', 'ip_address', 'country', 'device', 'os', 'browser', 'opens', 'first_opened_at', 'last_opened_at', 'created_at', 'updated_at'],
				'clicks' => ['first_name', 'link', 'clicks', 'first_clicked_at', 'last_clicked_at', 'created_at', 'updated_at'],
				'failures' => ['first_name', 'type', 'reason', 'fails', 'first_failed_at', 'last_failed_at', 'created_at', 'updated_at']
			];

			$type = in_array(strtolower($request->type), $types) ? strtolower($request->type) : $types[0];
			$orderBy = in_array(strtolower($request->orderBy), $orderByFields[$type]) ? strtolower($request->orderBy) : 'updated_at';
			$order = in_array(strtolower($request->order), $this->orderCriteria) ? strtolower($request->order) : $this->orderCriteria[0];
			$perPage = (int) $request->perPage ?: $this->perPage;
			$search = strtolower($request->search);

			$recipients = $search
				? Email::getRecipientSearchResults($search, (int) $id, $type, $orderBy, $order, $perPage)
				: Email::getRecipients( (int) $id, $type, [], $orderBy, $order, $perPage);

			return response()->json(compact('recipients'));
		}

		return response()->json(['error' => "$this->friendlyName does not exist"], 404);
	}

	/**
	 * Show specified resource general stats.
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getGeneralStats($id, Request $request)
	{
		$resource = Email::getGeneralStats( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "No stats found"], 404);
	}

	/**
	 * Show specified resource opens stats.
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getOpensStats($id, Request $request)
	{
		$limit = 5;
		$resource = Email::getOpensStats( (int) $id, $limit );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "No stats found"], 404);
	}

	/**
	 * Show specified resource clicks stats
	 * @param $id
	 * @param Request $request
	 *
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getClicksStats($id, Request $request)
	{
		if ( $request->user()->can('read', $this->policyOwnerClass) ) {

			$orderBy = in_array(strtolower($request->orderBy), ['clicks_count', 'link']) ? strtolower($request->orderBy) : 'clicks_count';
			$order = in_array(strtolower($request->order), $this->orderCriteria) ? strtolower($request->order) : 'DESC';
			$perPage = (int) $request->perPage ?: $this->perPage;
			$search = strtolower($request->search);

			$resource = $search
				? Email::getClicksStatsResults($search, (int) $id, $orderBy, $order, $perPage)
				: Email::getClicksStats( (int) $id, $orderBy, $order, $perPage);

			if ( $resource )
				return response()->json(compact('resource'));
			else
				return response()->json(['error' => "No stats found"], 404);
		}
		else
			return response()->json(['error' => 'You are not authorised to perform this action.'], 403);
	}

	/**
	 * Show specified resource failures stats.
	 * @param $id
	 * @param Request $request
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function getFailuresStats($id, Request $request)
	{
		$resource = Email::getFailuresStats( (int) $id );
		$currentUser = $request->user();

		if ( $resource ) {
			if ( ! $currentUser->can('read', $this->policyOwnerClass) )
				return response()->json(['error' => 'You are not authorised to perform this action.'], 403);

			return response()->json(compact('resource'));
		}

		return response()->json(['error' => "No stats found"], 404);
	}

	/**
	 * Display resource content
	 * @param $id
	 */
	public function display($id)
	{
		$id = (int) Hashids::decode($id);

		if ( $resource = Email::findResource($id) ) {
			$content = $resource->content;

			$decodedSubscriberId = request()->has('unique') && (int) request()->get('unique') ? (int) Hashids::decode(request()->get('unique')) : null;
			$subscriber = $decodedSubscriberId ? Subscriber::findResource($decodedSubscriberId) : null;

			if ( $decodedSubscriberId && $subscriber ) {
				$encodedSubscriberId = Hashids::encode($decodedSubscriberId);
				$encodedEmailId = Hashids::encode($id);

				$unsubscribeUrl = route('unsubscribe');
				$viewInBrowserUrl = route('emails.display', ['id' => $encodedEmailId]); //Not in Use

				$substitutionVariables = [
					'id'                             => '%id%',
					'first_name'                     => '%first_name%',
					'last_name'                      => '%last_name%',
					'name'                           => '%name%',
					'email'                          => '%email%',
					'unsubscribe'                    => '%unsubscribe%',
					'view_this_email_in_the_browser' => '%view_this_email_in_the_browser%',
				];

				foreach ( $substitutionVariables as $key => $variable ) {
					if ( $key === 'unsubscribe' )
						$content = str_ireplace( $variable, "<a href='{$unsubscribeUrl}?unique=$encodedSubscriberId'>unsubscribe</a>", $content );
					elseif ( $key === 'view_this_email_in_the_browser' )
						$content = str_ireplace( $variable, "<a href='#'>view this email in the browser</a>", $content );
					else
						$content = str_ireplace( $variable, $subscriber->{$key}, $content );
				}
			}


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
					$resources = Email::getResources(0, 0, 3, $resourceIds, -1)->pluck('id')->toArray();
					$successNum = 0;

					if ( $resources ) {
						switch ($update) {
							case 'delete':
								$successNum = Email::doBulkActions($resources, 'delete');
								break;
							case 'restore':
								$successNum = Email::doBulkActions($resources, 'restore');
								break;
							case 'destroy':
								$successNum = Email::doBulkActions($resources, 'destroy');
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

			$resources = Email::getResources(0, 0, 3, $resourceIds, $deleted);
			$fileName .= count($resourceIds) ? 'Specified-Items-' : 'All-Items-';
			$fileName .= Carbon::now()->toDateString();

			$exporter = new ResourceExporter($resources, $fileName);
			return $exporter->generateExcelExport('emails');
		}
		else
			return redirect()->back();
	}
}
