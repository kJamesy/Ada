<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 21/09/2017
 * Time: 14:58
 */

namespace App\Tracking\Traits;

use App\Delivery;

trait DeliveryTracking
{

	/**
	 * Record given delivery details
	 * @param $email_id
	 * @param $subscriber_id
	 * @param $delivered_at
	 *
	 * @return Delivery|array|\Illuminate\Database\Eloquent\Model|null|static
	 */
	public static function recordDelivery($email_id, $subscriber_id, $delivered_at)
	{
		$delivery = Delivery::findResourceBelongingTo($email_id, $subscriber_id);

		if ( $delivery )
			return $delivery;

		if ( validator()->make(compact('email_id', 'subscriber_id', 'delivered_at'), Delivery::$rules)->fails())
			return ['error' => 'Validation problem'];

		$delivery = new Delivery();
		$delivery->email_id = $email_id;
		$delivery->subscriber_id = $subscriber_id;
		$delivery->delivered_at = $delivered_at;
		$delivery->save();

		return $delivery;
	}

}