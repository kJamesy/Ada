<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Template extends Model
{
   use Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'description', 'content', 'last_editor', 'is_deleted'];

	/**
	 * Custom attributes
	 * @var array
	 */
	protected $appends = ['title'];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'name' => 'required|unique:templates|max:255',
		'description' => 'required|max:512',
		'content' => 'required|max:128000',
	];

	/**
	 * 'title' accessor
	 * @return string
	 */
	public function getTitleAttribute()
	{
		return $this->name;
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
	 * Find resource by id
	 * @param $id
	 * @return mixed
	 */
	public static function findResource($id)
	{
		return static::isNotDeleted()->find($id);
	}

	/**
	 * Get all resources
	 * @param array $selected
	 * @param int $deleted
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getResources($selected = [], $deleted = 0, $orderBy = 'updated_at', $order = 'desc', $paginate = null)
	{
		$query = static::with([]);

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
	 * @param int $paginate
	 * @param array $except
	 * @return mixed
	 */
	public static function getSearchResults($search, $deleted = 0, $paginate = 25)
	{
		$searchQuery = static::search($search);
		$searchQuery->limit = 500;
		$results = $searchQuery->get()->pluck('id');

		$query = static::whereIn('id', $results);

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
	 * Get resources that can be attached to others
	 * @return mixed
	 */
	public static function getAttachableResources()
	{
		return static::isNotDeleted()->orderBy('name')->get();
	}

}
