<?php

namespace App;

use App\Notifications\AdminPasswordReset;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Scout\Searchable;

class User extends Authenticatable
{
    use Notifiable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['first_name', 'last_name', 'username', 'email', 'password', 'active', 'meta'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Custom attributes
     * @var array
     */
    protected $appends = ['name', 'is_super_admin', 'is_user', 'last_login', 'penultimate_login', ];

    /**
     * Validation rules
     * @var array
     */
    public static $rules = [
        'first_name' => 'required|max:255',
        'last_name' => 'required|max:255',
        'username' => 'required|max:255|unique:users',
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|min:6|confirmed',
    ];

	/**
	 * A user has many Login Activities
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function login_activities()
	{
		return $this->hasMany(LoginActivity::class);
	}

	/**
	 * A User has many Emails
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
    public function emails()
    {
    	return $this->hasMany(Email::class);
    }

	/**
	 * A User has many EmailContents
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function email_contents()
	{
		return $this->hasMany(EmailContent::class);
	}

    /**
     * Get default user role
     * @return string
     */
    public static function getDefaultRole()
    {
        return static::first() ? 'User' : 'Super Administrator';
    }

    /**
     * 'name' accessor
     * @return string
     */
    public function getNameAttribute()
    {
        $name = "$this->first_name " . strtoupper($this->last_name);
        return $name;
    }

    /**
     * 'is_super_admin' accessor
     * @return bool
     */
    public function getIsSuperAdminAttribute()
    {
        $meta = json_decode($this->meta);

        if ( $meta && property_exists($meta, 'role') )
            return strtolower($meta->role) == 'super administrator';

        return false;
    }

    /**
     * 'is_user' accessor
     * @return bool
     */
    public function getIsUserAttribute()
    {
        $meta = json_decode($this->meta);

        if ( $meta && property_exists($meta, 'role') )
            return strtolower($meta->role) == 'user';

        return false;
    }

	/**
	 * 'last_login' accessor
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Query\Builder|null
	 */
	public function getLastLoginAttribute()
	{
		return $this->login_activities()->where('success', 1)->orderBy('created_at', 'DESC')->first();
	}

	/**
	 * 'penultimate_login' accessor
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany|\Illuminate\Database\Query\Builder|null
	 */
	public function getPenultimateLoginAttribute()
	{
		return $this->login_activities()->where('success', 1)->orderBy('created_at', 'DESC')->skip(1)->first();
	}

	/**
	 * Send the password reset notification.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function sendPasswordResetNotification($token)
	{
		$this->notify(new AdminPasswordReset($token, $this));
	}

    /**
     * Find resource by id
     * @param $id
     * @return mixed
     */
    public static function findResource($id)
    {
        return static::with('login_activities')->find($id);
    }

	/**
	 * Get all resources
	 * @param array $selected
	 * @param array $except
	 * @param string $orderBy
	 * @param string $order
	 * @param null $paginate
	 *
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getResources($selected = [], $except = [], $orderBy = 'first_name', $order = 'asc', $paginate = null)
	{
		$query = static::with('login_activities');

		if ( $selected )
			$query->whereIn('id', $selected);

		if ( $except )
			$query->whereNotIn('id', $except);

		$query->orderBy($orderBy, $order);

		return (int) $paginate ? $query->paginate($paginate) : $query->get();
	}

	/**
	 * Get search results
	 * @param $search
	 * @param int $paginate
	 *
	 * @return mixed
	 */
	public static function getSearchResults($search, $paginate = 25)
	{
		$searchQuery = static::search($search);
		$searchQuery->limit = 5000;
		$results = $searchQuery->get()->pluck('id');

		$query = static::with('login_activities')->whereIn('id', $results);

		return $query->paginate($paginate);
	}

	/**
	 * Get resources that are attached to emails
	 * @return \Illuminate\Database\Eloquent\Collection|static[]
	 */
	public static function getEmailAttachedResources()
	{
		return static::whereHas('emails', function($q){
			$q->isNotDeleted()->isNotDraft();
		})->orderBy('first_name')->get(['id', 'first_name', 'last_name']);
	}

}
