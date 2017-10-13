<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 21/09/2017
 * Time: 14:58
 */

namespace App\Tracking\Traits;

use App\Injection;

trait InjectionTracking
{

	/**
	 * Record given injection details
	 * @param $email_id
	 * @param $subscriber_id
	 * @param $injected_at
	 *
	 * @return Injection|array|\Illuminate\Database\Eloquent\Model|null|static
	 */
	public static function recordInjection($email_id, $subscriber_id, $injected_at)
	{
		$injection = Injection::findResourceBelongingTo($email_id, $subscriber_id);

		if ( $injection )
			return $injection;

		if ( validator()->make(compact('email_id', 'subscriber_id', 'injected_at'), Injection::$rules)->fails())
			return ['error' => 'Validation problem'];

		$injection = new Injection();
		$injection->email_id = $email_id;
		$injection->subscriber_id = $subscriber_id;
		$injection->injected_at = $injected_at;
		$injection->save();

		return $injection;
	}

}