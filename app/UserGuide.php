<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class UserGuide extends Model
{
    use Searchable;

	/**
	 * Custom attributes
	 * @var array
	 */
	protected $appends = ['has_parent', 'has_children'];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'parent_id' => 'nullable|exists:user_guides,id',
		'title' => 'required|max:255',
		'slug' => 'max:400',
		'content' => 'required|max:128000',
		'order' => 'required|integer|min:0|max:65535',
	];

	/**
	 * A UserGuide belongs to a parent
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function parent()
	{
		return $this->belongsTo(UserGuide::class, 'parent_id');
	}

	/**
	 * A UserGuide has many children
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function children()
	{
		return $this->hasMany(UserGuide::class, 'parent_id');
	}

	/**
	 * has_parent accessor
	 * @return bool
	 */
	public function getHasParentAttribute()
	{
		return !! $this->parent()->count();
	}

	/**
	 * has_children accessor
	 * @return bool
	 */
	public function getHasChildrenAttribute()
	{
		return !! $this->children()->count();
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
		return static::with(['parent', 'children'])->isNotDeleted()->find($id);
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
		$query = static::with(['parent', 'children']);

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
	 * @param int $paginate
	 * @param array $except
	 * @return mixed
	 */
	public static function getSearchResults($search, $deleted = 0, $paginate = 25)
	{
		$searchQuery = static::search($search);
		$searchQuery->limit = 5000;
		$results = $searchQuery->get()->pluck('id');

		$query = static::with(['parent', 'children'])->whereIn('id', $results);

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
	 * Find all existing slugs
	 * @return mixed
	 */
	public static function getExistingSlugs()
	{
		return static::get()->pluck('slug');
	}

	/**
	 * Get resources that can be attached to others
	 * @return mixed
	 */
	public static function getAttachableResources()
	{
		return static::isNotDeleted()->whereNull('parent_id')->orderBy('title')->get(['id', 'title']);
	}


	//FRONTEND METHODS

	/**
	 * Get a page by slug
	 * @param $slug
	 * @return mixed
	 */
	public static function getPageBySlug($slug)
	{
		return static::with(['parent', 'children'])->isNotDeleted()->where('slug', $slug)->first();
	}

	/**
	 * Get the landing page
	 * @return mixed
	 */
	public static function getHomePage()
	{
		$query = static::with(['parent', 'children'])
		               ->isNotDeleted()
		               ->whereNull('parent_id');

		return $query->where('slug', 'home')
		             ->orWhere('slug', 'home-page')
		             ->orWhere('slug', 'index')
		             ->first()
			?:
			$query->orderBy('order', 'ASC')->first();
	}

	/**
	 * Get/Cache all pages
	 * @return mixed|null
	 */
	public static function getCachedPages()
	{
		$pages = null;

		try {
			$pages = cache()->remember('user_guides', $minutes = (60*24), function() {
				return static::with(['parent', 'children'])->isNotDeleted()->orderBy('order', 'ASC')->get();
			});
		} catch(\Exception $e) {

		}

		return $pages;
	}

}
