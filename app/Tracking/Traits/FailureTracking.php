<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 25/09/2017
 * Time: 11:10
 */

namespace App\Tracking\Traits;


use App\Failure;

trait FailureTracking
{

	/**
	 * Record given fail details
	 * @param $email_id
	 * @param $subscriber_id
	 * @param $type
	 * @param $reason
	 * @param $failed_at
	 *
	 * @return Failure|array|\Illuminate\Database\Eloquent\Model|null|static
	 */
	public static function recordFailure($email_id, $subscriber_id, $type, $reason, $failed_at)
	{
		if ( validator()->make(compact('email_id', 'subscriber_id', 'type', 'reason'), Failure::$rules)->fails())
			return ['error' => 'Validation problem'];

		$failure = Failure::findResourceBelongingTo($email_id, $subscriber_id);

		$fails = $failure ? (int) $failure->fails + 1 : 1;
		$first_failed = $failure ? $failure->first_failed_at : $failed_at;

		if ( ! $failure )
			$failure = new Failure();

		$failure->email_id = $email_id;
		$failure->subscriber_id = $subscriber_id;
		$failure->type = trim($type);
		if ( $reason )
			$failure->reason = trim($reason);
		$failure->fails = $fails;
		$failure->first_failed_at = $first_failed;
		$failure->last_failed_at = $failed_at;

		$failure->save();

		return $failure;
	}

}