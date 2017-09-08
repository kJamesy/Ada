<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Email extends Model
{
	use Searchable;

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'subscribers' => 'required_without:mailing_lists',
		'mailing_lists' => 'required_without:subscribers',
		'campaign' => 'required|exists:campaigns,id',
		'subject' => 'required|max:255',
		'content' => 'required|max:48000',
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
		return $query->where('status', (int) $status);
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
	public static function getResources($userId = 0, $campaignId = 0, $status = 1, $selected = [], $deleted = 0, $orderBy = 'updated_at', $order = 'desc', $paginate = null)
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
	 * @return int
	 */
	public static function getCount($deleted = 0)
	{
		if ( (int) $deleted == 1 )
			return static::isDeleted()->count();
		elseif ( (int) $deleted == 0 )
			return static::isNotDeleted()->count();

		return static::count();
	}

}
