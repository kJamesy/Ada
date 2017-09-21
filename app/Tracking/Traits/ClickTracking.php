<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 21/09/2017
 * Time: 14:59
 */

namespace App\Tracking\Traits;


use App\Click;

trait ClickTracking
{

	/**
	 * Record given click details
	 * @param $email_id
	 * @param $subscriber_id
	 * @param $link
	 * @param $clicked_at
	 *
	 * @return Click|array|\Illuminate\Database\Eloquent\Model|null|static
	 */
	public static function recordClick($email_id, $subscriber_id, $link, $clicked_at)
	{
		if ( validator()->make(compact('email_id', 'subscriber_id', 'link'), Click::$rules)->fails())
			return ['error' => 'Validation problem'];

		$click = Click::findResourceBelongingTo($email_id, $subscriber_id, trim($link));

		$clicks = $click ? (int) $click->clicks + 1 : 1;
		$first_clicked = $click ? $click->first_clicked_at : $clicked_at;

		if ( ! $click )
			$click = new Click();

		$click->email_id = $email_id;
		$click->subscriber_id = $subscriber_id;
		$click->link = trim($link);
		$click->clicks = $clicks;
		$click->first_clicked_at = $first_clicked;
		$click->last_clicked_at = $clicked_at;

		$click->save();

		return $click;
	}


}