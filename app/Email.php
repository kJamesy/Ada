<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
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
		'content' => 'required|max:48000',
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
		$status = (int) $status;
		return $status === 2 ? $query->where('status', '<>', -2) : $query->where('status', $status);
	}

	/**
	 * Find resource by id
	 * @param $id
	 * @return mixed
	 */
	public static function findResource($id)
	{
		return static::with('user')->with('campaign')->find($id);
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

		if ( (int) $deleted == 1 )
			$query->isDeleted();
		elseif ( (int) $deleted == 0 )
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
		$query = static::with('user')->with('campaign')->status($status)->whereIn('id', static::search($search)->get()->pluck('id'));

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

}
