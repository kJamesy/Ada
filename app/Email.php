<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Laravel\Scout\Searchable;

class Email extends Model
{
	use Searchable;

	/**
	 * Custom attributes
	 * @var array
	 */
	protected $appends = ['friendly_status'];

	/**
	 * The attributes that should be mutated to dates.
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
		'sent_at'
	];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'sender_name' => 'required|max:255',
		'sender_email' => 'required|email|max:255',
		'reply_to_email' => 'required|email|max:255',
		'subscribers' => 'required_without:mailing_lists',
		'mailing_lists' => 'required_without:subscribers',
		'campaign' => 'required|exists:campaigns,id',
		'subject' => 'required|max:255',
		'content' => 'required|max:128000',
	];

	/**
	 * All statuses defined
	 * @var array
	 */
	public static $statuses = [
		-2 => 'draft',
		-1 => 'scheduled',
		0 => 'failed',
		1 => 'success',
		2 => 'sent'
	];

	/**
	 * An Email belongs to a User
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	/**
	 * An Email belongs in a Campaign
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function campaign()
	{
		return $this->belongsTo(Campaign::class);
	}

	/**
	 * An Email has many Injections
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function injections()
	{
		return $this->hasMany(Injection::class);
	}

	/**
	 * An Email has many Deliveries
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function deliveries()
	{
		return $this->hasMany(Delivery::class);
	}

	/**
	 * An Email has many Opens
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function opens()
	{
		return $this->hasMany(Open::class);
	}

	/**
	 * An Email has many Clicks
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function clicks()
	{
		return $this->hasMany(Click::class);
	}

	/**
	 * An Email has many Failures
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function failures()
	{
		return $this->hasMany(Failure::class);
	}

	/**
	 * 'friendly_status' accessor
	 * @return false|int|string
	 */
	public function getFriendlyStatusAttribute()
	{
		return array_key_exists($this->status, self::$statuses) ? ucfirst(self::$statuses[$this->status]) : 'Unknown';
	}

	/**
	 * Scope for deleted model
	 * @param $query
	 * @return mixed
	 */
	public function scopeIsDeleted($query)
	{
		return $query->where('is_deleted', 1);
	}

	/**
	 * Scope for is not deleted model
	 * @param $query
	 * @return mixed
	 */
	public function scopeIsNotDeleted($query)
	{
		return $query->where('is_deleted', 0);
	}

	/**
	 * Scope for emails by supplied user
	 * @param $query
	 * @param $userId
	 *
	 * @return mixed
	 */
	public function scopeByUser($query, $userId)
	{
		return $query->where('user_id', (int) $userId);
	}

	/**
	 * Scope for emails in given campaign
	 * @param $query
	 * @param $campaignId
	 *
	 * @return mixed
	 */
	public function scopeInCampaign($query, $campaignId)
	{
		return $query->where('campaign_id', (int) $campaignId);
	}

	/**
	 * Scope for emails with given status
	 * @param $query
	 * @param $status
	 *
	 * @return mixed
	 */
	public function scopeStatus($query, $status)
	{
		switch((int) $status) {
			case 2:
				return $query->isNotDraft();
				break;
			case 3:
				return $query;
				break;
			default:
				return $query->where('status', (int) $status);
				break;
		}
	}

	/**
	 * Scope for email drafts
	 * @param $query
	 *
	 * @return mixed
	 */
	public function scopeIsDraft($query)
	{
		return $query->where('status', -2);
	}

	/**
	 * Scope for email drafts
	 * @param $query
	 *
	 * @return mixed
	 */
	public function scopeIsNotDraft($query)
	{
		return $query->where('status', '<>', -2);
	}

	/**
	 * Find resource by id
	 * @param $id
	 * @return mixed
	 */
	public static function findResource($id)
	{
		return static::with('user')
		             ->with('campaign')
		             ->withCount(['injections', 'deliveries', 'opens'])
		             ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM clicks WHERE email_id = $id) AS clicks_count")
		             ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM failures WHERE email_id = $id) AS failures_count")
		             ->isNotDeleted()->find($id);
	}

	/**
	 * Get all resources
	 * @param int $userId
	 * @param int $campaignId
	 * @param int $status
	 * @param array $selected
	 * @param int $deleted
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return mixed
	 */
	public static function getResources($userId = 0, $campaignId = 0, $status = 2, $selected = [], $deleted = 0, $orderBy = 'updated_at', $order = 'desc', $paginate = null)
	{
		$query = static::with('user')->with('campaign')->status($status);

		if ( (int) $userId )
			$query->byUser($userId);

		if ( (int) $campaignId )
			$query->inCampaign($campaignId);

		if ( count($selected) )
			$query->whereIn('id', $selected);

		if ( (int) $deleted === 1 )
			$query->isDeleted();
		elseif ( (int) $deleted === 0 )
			$query->isNotDeleted();

		$query->orderBy($orderBy, $order);

		return (int) $paginate ? $query->paginate($paginate) : $query->get();
	}

	/**
	 * Get search results
	 * @param $search
	 * @param int $userId
	 * @param int $campaignId
	 * @param int $status
	 * @param int $deleted
	 * @param int $paginate
	 *
	 * @return mixed
	 */
	public static function getSearchResults($search, $userId = 0, $campaignId = 0, $status = 1, $deleted = 0, $paginate = 25)
	{
		$searchQuery = static::search($search);
		$searchQuery->limit = 5000;
		$results = $searchQuery->get()->pluck('id');

		$query = static::with('user')->with('campaign')->status($status)->whereIn('id', $results);

		if ( (int) $userId )
			$query->byUser($userId);

		if ( (int) $campaignId )
			$query->inCampaign($campaignId);

		if ( (int) $deleted == 1 )
			$query->isDeleted();
		elseif ( (int) $deleted == 0 )
			$query->isNotDeleted();

		return $query->paginate($paginate);
	}

	/**
	 * Get count of resources - type specified
	 * @param int $deleted
	 * @param int $status
	 *
	 * @return mixed
	 */
	public static function getCount($deleted = 0, $status = 2)
	{
		$query = static::status($status);

		if ( (int) $deleted == 1 )
			return $query->isDeleted()->count();
		elseif ( (int) $deleted == 0 )
			return $query->isNotDeleted()->count();

		return $query->count();
	}

	/**
	 * Perform specified bulk action
	 * @param $selected
	 * @param $verb
	 * @return int
	 */
	public static function doBulkActions($selected, $verb)
	{
		$count = 0;
		if ( is_array($selected) && count($selected) ) {

			switch( $verb ) {
				case 'delete':
					static::whereIn('id', $selected)->update(['is_deleted' => 1, 'updated_at' => Carbon::now()]);
					$count = count($selected);
					break;
				case 'restore':
					static::whereIn('id', $selected)->update(['is_deleted' => 0, 'updated_at' => Carbon::now()]);
					$count = count($selected);
					break;
				case 'destroy':
					static::whereIn('id', $selected)->delete();
					$count = count($selected);
					break;
			}
		}

		return $count;
	}

	/**
	 * Get recipients for a supplied email id
	 * @param $emailId
	 * @param string $type
	 * @param array $selected
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return mixed
	 */
	public static function getRecipients($emailId, $type = 'injections', $selected = [], $orderBy = 'updated_at', $order = 'desc', $paginate = null)
	{
		if ( $type === 'deliveries' ) {
			$query = Delivery::join('subscribers', 'deliveries.subscriber_id', '=', 'subscribers.id')
			                 ->where('deliveries.email_id', $emailId)
			                 ->orderBy($orderBy === 'first_name' ? 'subscribers.first_name' : "deliveries.$orderBy", $order);

			$rawQuery = "(case when ((select count(*) from deliveries WHERE email_id = deliveries.email_id AND subscriber_id = deliveries.subscriber_id) > 0)  THEN  1 ELSE  0 END) as delivered,";
			$rawQuery .= "(case when ((select count(*) from opens WHERE email_id = deliveries.email_id AND subscriber_id = deliveries.subscriber_id) > 0)  THEN  1 ELSE  0 END) as opened,";
			$rawQuery .= "(case when ((select count(*) from clicks WHERE email_id = deliveries.email_id AND subscriber_id = deliveries.subscriber_id) > 0)  THEN  1 ELSE  0 END) as clicked,";
			$rawQuery .= "(case when ((select count(*) from failures WHERE email_id = deliveries.email_id AND subscriber_id = deliveries.subscriber_id) > 0)  THEN  1 ELSE  0 END) as failed";

			$query->select('subscribers.*', 'deliveries.*', DB::raw($rawQuery));
		}
		elseif ( $type === 'opens' ) {
			$query = Open::join('subscribers', 'opens.subscriber_id', '=', 'subscribers.id')
			                 ->where('opens.email_id', $emailId)
			                 ->orderBy($orderBy === 'first_name' ? 'subscribers.first_name' : "opens.$orderBy", $order);

			$rawQuery = "(case when ((select count(*) from deliveries WHERE email_id = opens.email_id AND subscriber_id = opens.subscriber_id) > 0)  THEN  1 ELSE  0 END) as delivered,";
			$rawQuery .= "(case when ((select count(*) from opens WHERE email_id = opens.email_id AND subscriber_id = opens.subscriber_id) > 0)  THEN  1 ELSE  0 END) as opened,";
			$rawQuery .= "(case when ((select count(*) from clicks WHERE email_id = opens.email_id AND subscriber_id = opens.subscriber_id) > 0)  THEN  1 ELSE  0 END) as clicked,";
			$rawQuery .= "(case when ((select count(*) from failures WHERE email_id = opens.email_id AND subscriber_id = opens.subscriber_id) > 0)  THEN  1 ELSE  0 END) as failed";

			$query->select('subscribers.*', 'opens.*', DB::raw($rawQuery));
		}
		elseif ( $type === 'clicks' ) {
			$query = Click::join('subscribers', 'clicks.subscriber_id', '=', 'subscribers.id')
			                  ->where('clicks.email_id', $emailId)
			                  ->orderBy($orderBy === 'first_name' ? 'subscribers.first_name' : "clicks.$orderBy", $order);

			$rawQuery = "(case when ((select count(*) from deliveries WHERE email_id = clicks.email_id AND subscriber_id = clicks.subscriber_id) > 0)  THEN  1 ELSE  0 END) as delivered,";
			$rawQuery .= "(case when ((select count(*) from opens WHERE email_id = clicks.email_id AND subscriber_id = clicks.subscriber_id) > 0)  THEN  1 ELSE  0 END) as opened,";
			$rawQuery .= "(case when ((select count(*) from clicks WHERE email_id = clicks.email_id AND subscriber_id = clicks.subscriber_id) > 0)  THEN  1 ELSE  0 END) as clicked,";
			$rawQuery .= "(case when ((select count(*) from failures WHERE email_id = clicks.email_id AND subscriber_id = clicks.subscriber_id) > 0)  THEN  1 ELSE  0 END) as failed";

			$query->select('subscribers.*', 'clicks.*', DB::raw($rawQuery));
		}
		elseif ( $type === 'failures' ) {
			$query = Failure::join('subscribers', 'failures.subscriber_id', '=', 'subscribers.id')
			              ->where('failures.email_id', $emailId)
			              ->orderBy($orderBy === 'first_name' ? 'subscribers.first_name' : "failures.$orderBy", $order);

			$rawQuery = "(case when ((select count(*) from deliveries WHERE email_id = failures.email_id AND subscriber_id = failures.subscriber_id) > 0)  THEN  1 ELSE  0 END) as delivered,";
			$rawQuery .= "(case when ((select count(*) from opens WHERE email_id = failures.email_id AND subscriber_id = failures.subscriber_id) > 0)  THEN  1 ELSE  0 END) as opened,";
			$rawQuery .= "(case when ((select count(*) from clicks WHERE email_id = failures.email_id AND subscriber_id = failures.subscriber_id) > 0)  THEN  1 ELSE  0 END) as clicked,";
			$rawQuery .= "(case when ((select count(*) from failures WHERE email_id = failures.email_id AND subscriber_id = failures.subscriber_id) > 0)  THEN  1 ELSE  0 END) as failed";

			$query->select('subscribers.*', 'failures.*', DB::raw($rawQuery));
		}
		else {
			$query = Injection::join('subscribers', 'injections.subscriber_id', '=', 'subscribers.id')
			             ->where('injections.email_id', $emailId)
			             ->orderBy($orderBy === 'first_name' ? 'subscribers.first_name' : "injections.$orderBy", $order);

			$rawQuery = "(case when ((select count(*) from deliveries WHERE email_id = injections.email_id AND subscriber_id = injections.subscriber_id) > 0)  THEN  1 ELSE  0 END) as delivered,";
			$rawQuery .= "(case when ((select count(*) from opens WHERE email_id = injections.email_id AND subscriber_id = injections.subscriber_id) > 0)  THEN  1 ELSE  0 END) as opened,";
			$rawQuery .= "(case when ((select count(*) from clicks WHERE email_id = injections.email_id AND subscriber_id = injections.subscriber_id) > 0)  THEN  1 ELSE  0 END) as clicked,";
			$rawQuery .= "(case when ((select count(*) from failures WHERE email_id = injections.email_id AND subscriber_id = injections.subscriber_id) > 0)  THEN  1 ELSE  0 END) as failed";

			$query->select('subscribers.*', 'injections.*', DB::raw($rawQuery));
		}

		if ( count($selected) ) {
			$query->whereIn('subscribers.id', $selected['ids']);
		}

		return (int) $paginate ? $query->paginate($paginate) : $query->get();
	}


	/**
	 * Get recipients search results
	 * @param $search
	 * @param $emailId
	 * @param string $type
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return mixed
	 */
	public static function getRecipientSearchResults($search, $emailId, $type = 'injections', $orderBy = 'updated_at', $order = 'desc', $paginate = null)
	{
		$searchQuery = Subscriber::search($search);
		$searchQuery->limit = 5000;
		$results = $searchQuery->get()->pluck('id');

		return static::getRecipients($emailId, $type, ['ids' => $results], $orderBy, $order, $paginate);
	}

	/**
	 * Get general email stats
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getGeneralStats($id)
	{
		return static::withCount(['injections', 'deliveries', 'opens'])
		             ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM clicks WHERE email_id = $id) AS clicks_count")
		             ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM failures WHERE email_id = $id) AS failures_count")
		             ->where('status', 1)
		             ->isNotDeleted()
		             ->find($id);
	}

	/**
	 * Get stats for a given email
	 * @param $id
	 * @param $limit
	 *
	 * @return mixed
	 */
	public static function getOpensStats($id, $limit)
	{
		$email = static::withCount(['injections', 'deliveries', 'opens'])
		               ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM clicks WHERE email_id = $id) AS clicks_count")
		               ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM failures WHERE email_id = $id) AS failures_count")
		               ->where('status', 1)->isNotDeleted()->find($id);

		if ( $email ) {
			$email->country_stats = static::getCountriesStats( $id, $limit );
			$email->device_stats  = static::getDevicesStats( $id, $limit );
			$email->os_stats      = static::getOssStats( $id, $limit );
			$email->browser_stats = static::getBrowsersStats( $id, $limit );
		}

		return $email;
	}

	/**
	 * Get countries stats for specified email id
	 * @param $id
	 * @param $limit
	 *
	 * @return mixed
	 */
	public static function getCountriesStats($id, $limit)
	{
		return DB::select("(SELECT IF(all_countries.country_count >= IFNULL(nth_count.c_count, 0), all_countries.country, 'Others') AS country_name,
		SUM(all_countries.country_count) AS country_count
		FROM (SELECT country, count(country) AS country_count FROM opens WHERE email_id = $id GROUP BY country) AS all_countries
		LEFT JOIN (SELECT count(country) c_count FROM opens WHERE email_id = $id GROUP BY country ORDER BY c_count DESC LIMIT $limit, 1) AS nth_count
		ON true
		GROUP BY country_name
		ORDER BY country_count DESC)");
	}

	/**
	 * Get devices stats for specified email id
	 * @param $id
	 * @param $limit
	 *
	 * @return mixed
	 */
	public static function getDevicesStats($id, $limit)
	{
		return DB::select("(SELECT IF(all_devices.device_count >= IFNULL(nth_count.d_count, 0), all_devices.device, 'Others') AS device_name,
		SUM(all_devices.device_count) AS device_count
		FROM (SELECT device, count(device) AS device_count FROM opens WHERE email_id = $id GROUP BY device) AS all_devices
		LEFT JOIN (SELECT count(device) d_count FROM opens WHERE email_id = $id GROUP BY device ORDER BY d_count DESC LIMIT $limit, 1) AS nth_count
		ON true
		GROUP BY device_name
		ORDER BY device_count DESC)");
	}

	/**
	 * Get OSs stats for specified email id
	 * @param $id
	 * @param $limit
	 *
	 * @return mixed
	 */
	public static function getOssStats($id, $limit)
	{
		return DB::select("(SELECT IF(all_OSs.OS_count >= IFNULL(nth_count.o_count, 0), all_OSs.OS, 'Others') AS OS_name,
		SUM(all_OSs.OS_count) AS OS_count
		FROM (SELECT OS, count(OS) AS OS_count FROM opens WHERE email_id = $id GROUP BY OS) AS all_OSs
		LEFT JOIN (SELECT count(os) o_count FROM opens WHERE email_id = $id GROUP BY OS ORDER BY o_count DESC LIMIT $limit, 1) AS nth_count
		ON true
		GROUP BY OS_name
		ORDER BY OS_count DESC)");
	}

	/**
	 * Get browsers stats for specified email id
	 * @param $id
	 * @param $limit
	 *
	 * @return mixed
	 */
	public static function getBrowsersStats($id, $limit)
	{
		return DB::select("(SELECT IF(all_browsers.browser_count >= IFNULL(nth_count.b_count, 0), all_browsers.browser, 'Others') AS browser_name,
		SUM(all_browsers.browser_count) AS browser_count
		FROM (SELECT browser, count(browser) AS browser_count FROM opens WHERE email_id = $id GROUP BY browser) AS all_browsers
		LEFT JOIN (SELECT count(browser) b_count FROM opens WHERE email_id = $id GROUP BY browser ORDER BY b_count DESC LIMIT $limit, 1) AS nth_count
		ON true
		GROUP BY browser_name
		ORDER BY browser_count DESC)");
	}

	/**
	 * Get clicks stats
	 * @param null $emailId
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return mixed
	 */
	public static function getClicksStats($emailId = null, $orderBy = 'clicks_count', $order = 'DESC', $paginate = null)
	{
		$email = static::where('status', 1)->isNotDeleted()->find($emailId);

		if ( $email ) {
			$query = Click::select('link', DB::raw('count(link) clicks_count'))
			              ->where('email_id', (int) $emailId)
			              ->groupBy('link')
			              ->orderBy($orderBy, $order);

			$email->clicks_stats = (int) $paginate ? $query->paginate($paginate) : $query->get();
		}

		return $email;
	}

	/**
	 * Get clicks stats search results
	 * @param $search
	 * @param $emailId
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return mixed
	 */
	public static function getClicksStatsResults($search, $emailId, $orderBy = 'clicks_count', $order = 'DESC', $paginate = null)
	{
		$searchQuery = Click::search($search);
		$searchQuery->limit = 5000;

		$email = static::where('status', 1)->isNotDeleted()->find($emailId);

		if ( $email ) {
			$query = Click::select('link', DB::raw('count(link) clicks_count'))
			              ->whereIn('id', $searchQuery->get()->pluck('id'))
			              ->where('email_id', (int) $emailId)
			              ->groupBy('link')
			              ->orderBy($orderBy, $order);

			$email->clicks_stats = (int) $paginate ? $query->paginate($paginate) : $query->get();
		}

		return $email;
	}

	/**
	 * Get failures stats
	 * @param $id
	 *
	 * @return mixed
	 */
	public static function getFailuresStats($id)
	{
		$email = static::withCount(['injections', 'deliveries', 'opens'])
		               ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM clicks WHERE email_id = $id) AS clicks_count")
		               ->selectRaw("(SELECT COUNT(DISTINCT subscriber_id) FROM failures WHERE email_id = $id) AS failures_count")
		               ->where('status', 1)->isNotDeleted()->find($id);

		if ( $email )
			$email->failures_stats = DB::select("(SELECT type, COUNT(type) AS types_count FROM failures WHERE email_id = $id GROUP BY type ORDER BY types_count DESC)");

		return $email;
	}


}
