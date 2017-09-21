<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Subscriber extends Model
{
    use Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['first_name', 'last_name', 'email', 'active', 'is_deleted'];

	/**
	 * Custom attributes
	 * @var array
	 */
	protected $appends = ['name', 'label'];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'first_name' => 'required|max:255',
		'last_name' => 'required|max:255',
		'email' => 'required|email|max:255|unique:subscribers'
	];

	/**
	 * Many to Many relationship
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
	 */
	public function mailing_lists()
	{
		return $this->belongsToMany(MailingList::class, 'mailing_list_subscriber', 'subscriber_id', 'mailing_list_id');
	}

	/**
	 * A Subscriber has many Deliveries
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function deliveries()
	{
		return $this->hasMany(Delivery::class);
	}

	/**
	 * A Subscriber has many Opens
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function opens()
	{
		return $this->hasMany(Open::class);
	}

	/**
	 * A Subscriber has many Clicks
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function clicks()
	{
		return $this->hasMany(Click::class);
	}

	/**
	 * 'name' accessor
	 * @return string
	 */
	public function getNameAttribute()
	{
		$name = "$this->first_name $this->last_name";
		return $name;
	}

	/**
	 * 'label' accessor
	 * @return string
	 */
	public function getLabelAttribute()
	{
		$name = "$this->first_name $this->last_name <$this->email>";
		return $name;
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
	 * Scope for active model
	 * @param $query
	 * @return mixed
	 */
	public function scopeIsActive($query)
	{
		return $query->where('active', 1);
	}

	/**
	 * Scope for is not active model
	 * @param $query
	 * @return mixed
	 */
	public function scopeIsNotActive($query)
	{
		return $query->where('active', 0);
	}

	/**
	 * Scope for subscribers in given mailing lists
	 * @param $query
	 * @param $mListIds
	 * @return mixed
	 */
	public function scopeInMailingLists($query, $mListIds)
	{
		return $query->whereHas('mailing_lists', function($query) use ($mListIds) {
			$query->whereIn('id', (array) $mListIds);
		});
	}

	/**
	 * Scope for subscribers not attached to any mailing list
	 * @param $query
	 * @return mixed
	 */
	public function scopeIsUnattached($query)
	{
		return $query->whereDoesntHave('mailing_lists');
	}

	/**
	 * Find resource by id
	 * @param $id
	 * @return mixed
	 */
	public static function findResource($id)
	{
		return static::with('mailing_lists')->isNotDeleted()->find($id);
	}

	/**
	 * Find resource by email
	 * @param $email
	 * @return Model|null|static
	 */
	public static function findResourceByEmail($email)
	{
		return static::with('mailing_lists')->where('email', $email)->first();
	}

	/**
	 * Get all resources
	 *
	 * @param int $mListId
	 * @param array $selected
	 * @param int $deleted
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getResources( $mListId = 0, $selected = [], $deleted = 0, $orderBy = 'updated_at', $order = 'desc', $paginate = null)
	{
		$query = static::with([]);

		if ( $mListId === -1 )
			$query->isUnattached();
		elseif ( $mListId )
			$query->inMailingLists([$mListId]);

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
	 *
	 * @param $search
	 * @param int $mListId
	 * @param int $deleted
	 * @param int $paginate
	 *
	 * @return mixed
	 * @internal param array $except
	 */
	public static function getSearchResults($search, $mListId = 0, $deleted = 0, $paginate = 25)
	{
		$query = static::whereIn('id', static::search($search)->get()->pluck('id'));

		if ( $mListId )
			$query->inMailingLists([$mListId]);

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
				case 'activate':
					static::whereIn('id', $selected)->update(['active' => 1, 'updated_at' => Carbon::now()]);
					$count = count($selected);
					break;
				case 'deactivate':
					static::whereIn('id', $selected)->update(['active' => 0, 'updated_at' => Carbon::now()]);
					$count = count($selected);
					break;
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
	 * Get resources that are attachable
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getAttachableResources()
	{
		return static::isActive()->isNotDeleted()->orderBy('first_name')->get();
	}

	/**
	 * Get specified resources that are attachable
	 * @param array $selected
	 * @return mixed
	 */
	public static function getSpecifiedAttachableResources($selected=[])
	{
		return static::isActive()->isNotDeleted()->whereIn('id', $selected)->get();
	}

	/**
	 * Get attachable resources by specified mailing list ids
	 * @param array $mLists
	 * @return mixed
	 */
	public static function getAttachableResourcesBySpecifiedMLists($mLists = [])
	{
		return static::isActive()->isNotDeleted()->inMailingLists($mLists)->get();
	}

}
