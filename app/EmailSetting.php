<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class EmailSetting extends Model
{
	use Searchable;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'description', 'setting_value'];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'name' => 'required|unique:email_settings|max:255',
		'description' => 'required|max:512',
		'setting_value' => 'required|max:512'
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
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getResources($selected = [], $orderBy = 'updated_at', $order = 'desc', $paginate = null)
	{
		$query = static::with([]);

		if ( count($selected) )
			$query->whereIn('id', $selected);

		$query->orderBy($orderBy, $order);

		return (int) $paginate ? $query->paginate($paginate) : $query->get();
	}

	/**
	 * Get search results
	 * @param $search
	 * @param int $paginate
	 * @return mixed
	 */
	public static function getSearchResults($search, $paginate = 25)
	{
		return static::whereIn('id', static::search($search)->get()->pluck('id'))->paginate($paginate);
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
					static::whereIn('id', $selected)->delete();
					$count = count($selected);
					break;
			}
		}

		return $count;
	}

}
