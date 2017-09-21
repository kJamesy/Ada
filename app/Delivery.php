<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Delivery extends Model
{
    use Searchable;

	/**
	 * The attributes that should be mutated to dates.
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
		'delivered_at'
	];

	/**
	 * Validation rules
	 * @var array
	 */
	public static $rules = [
		'email_id' => 'required|exists:emails,id',
		'subscriber_id' => 'required|exists:subscribers,id',
		'delivered_at' => 'required',
	];

	/**
	 * A Delivery belongs to an Email
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function email()
	{
		return $this->belongsTo(Email::class);
	}

	/**
	 * A Delivery belongs in a Subscriber
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function subscriber()
	{
		return $this->belongsTo(Subscriber::class);
	}

	/**
	 * Find resource by id
	 * @param $id
	 *
	 * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
	 */
	public static function findResource($id)
	{
		return static::with('email')->with('subscriber')->find($id);
	}

	/**
	 * Find resource with given conditions
	 * @param $email_id
	 * @param $subscriber_id
	 *
	 * @return Model|null|static
	 */
	public static function findResourceBelongingTo($email_id, $subscriber_id)
	{
		return static::with('email')->with('subscriber')->where('email_id', $email_id)->where('subscriber_id', $subscriber_id)->first();
	}


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

}
