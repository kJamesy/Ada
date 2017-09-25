<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 21/09/2017
 * Time: 14:59
 */

namespace App\Tracking\Traits;


use App\Click;
use App\Open;

trait ClickTracking
{

	/**
	 * Record given click details
	 * @param $email_id
	 * @param $subscriber_id
	 * @param $user_agent
	 * @param $ip_address
	 * @param $country_code
	 * @param $link
	 * @param $clicked_at
	 *
	 * @return Click|array|\Illuminate\Database\Eloquent\Model|null|static
	 */
	public static function recordClick($email_id, $subscriber_id, $user_agent, $ip_address, $country_code, $link, $clicked_at)
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

		// Check if open was not recorded (they didn't enable images) and record it
		$open = Open::findResourceBelongingTo($email_id, $subscriber_id);

		if ( ! $open ) {
			try {
				$country_name = static::getCountryNameFromAlpha2Code($country_code);
				$device_name = static::getDeviceNameFromUserAgent($user_agent);
				$os = static::getOSFromUserAgent($user_agent);
				$browser = static::getBrowserFromUserAgent($user_agent);

				$open = new Open();

				$open->email_id = $email_id;
				$open->subscriber_id = $subscriber_id;
				$open->ip_address = $ip_address;
				if ( $country_name )
					$open->country = $country_name;
				if ( $device_name )
					$open->device = $device_name;
				if ( $os )
					$open->os = $os;
				if ( $browser )
					$open->browser = $browser;
				$open->user_agent = $user_agent;
				$open->first_opened_at = $clicked_at;
				$open->last_opened_at = $clicked_at;

				$open->save();
			}
			catch (\Exception $e) {
				//Nuffink
			}
		}

		return $click;
	}


}