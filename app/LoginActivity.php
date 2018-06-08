<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LoginActivity extends Model
{
	/**
	 * @var array
	 */
	protected $dates = ['created_at', 'updated_at', 'attempted_at'];

	/**
	 * A LoginActivity belongs to a User
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
