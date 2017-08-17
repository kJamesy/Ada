<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class MailingList extends Model
{
    use Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'description', 'is_deleted'];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'name' => 'required|unique:mailing_lists|max:255',
		'description' => 'required|max:512'
	];

	/**
	 * Find resource by id
	 * @param $id
	 * @return mixed
	 */
	public static function findResource($id)
	{
		return static::find($id);
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
			$query->where('is_deleted', 1);
		elseif ( (int) $deleted == 0 )
			$query->where('is_deleted', 0);

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
		$query = static::whereIn('id', static::search($search)->get()->pluck('id'));

		if ( (int) $deleted == 1 )
			$query->where('is_deleted', 1);
		elseif ( (int) $deleted == 0 )
			$query->where('is_deleted', 0);

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
			return static::where('is_deleted', 1)->count();
		elseif ( (int) $deleted == 0 )
			return static::where('is_deleted', 0)->count();

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

}
