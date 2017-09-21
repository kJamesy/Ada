<?php
/**
 * Created by PhpStorm.
 * User: James
 * Date: 21/09/2017
 * Time: 14:58
 */

namespace App\Tracking\Traits;


use App\Open;
use DeviceDetector\DeviceDetector;
use League\ISO3166\ISO3166;
use UAParser\Parser;

trait OpenTracking
{

	/**
	 * Record given open details
	 * @param $email_id
	 * @param $subscriber_id
	 * @param $user_agent
	 * @param $ip_address
	 * @param $country_code
	 * @param $opened_at
	 *
	 * @return Open|array|\Illuminate\Database\Eloquent\Model|null|static
	 */
	public static function recordOpen($email_id, $subscriber_id, $user_agent, $ip_address, $country_code, $opened_at)
	{
		if ( validator()->make(compact('email_id', 'subscriber_id'), Open::$rules)->fails())
			return ['error' => 'Validation problem'];

		$open = Open::findResourceBelongingTo($email_id, $subscriber_id);
		$country_name = static::getCountryNameFromAlpha2Code($country_code);
		$device_name = static::getDeviceNameFromUserAgent($user_agent);
		$os = static::getOSFromUserAgent($user_agent);
		$browser = static::getBrowserFromUserAgent($user_agent);

		$opens = $open ? (int) $open->opens + 1 : 1;
		$first_opened = $open ? $open->first_opened_at : $opened_at;

		if ( ! $open )
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
		$open->opens = $opens;
		$open->first_opened_at = $first_opened;
		$open->last_opened_at = $opened_at;

		$open->save();

		return $open;
	}

	/**
	 * Get Country name from ISO3166 alpha2 code
	 * @param $code
	 *
	 * @return mixed
	 */
	public static function getCountryNameFromAlpha2Code($code)
	{
		try {
			$league = new ISO3166();

			return $league->alpha2($code)['name'];
		}
		catch (\Exception $e) {
			return $code;
		}
	}

	/**
	 * Get device name from user agent string
	 * @param $user_agent
	 *
	 * @return null|string
	 */
	public static function getDeviceNameFromUserAgent($user_agent)
	{
		try {
			$device_detector = new DeviceDetector($user_agent);
			$device_detector->parse();

			return ucfirst($device_detector->getDeviceName());
		}
		catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Get operating system name and version from user agent string
	 * @param $user_agent
	 *
	 * @return null|string
	 */
	public static function getOSFromUserAgent($user_agent)
	{
		try {
			$device_detector = new DeviceDetector($user_agent);
			$device_detector->parse();

			$osName = $device_detector->getOs('name');
			$osVersion = $device_detector->getOs('version');

			return "$osName $osVersion";
		}
		catch (\Exception $e) {
			return null;
		}
	}

	/**
	 * Get browser family and major version from user agent string
	 * @param $user_agent
	 *
	 * @return null|string
	 */
	public static function getBrowserFromUserAgent($user_agent)
	{
		try {
			$ua_parser = Parser::create();
			$result = $ua_parser->parse($user_agent);

			$browser_family = $result->ua->family;
			$browser_major_version = $result->ua->major;

			return "$browser_family $browser_major_version";
		}
		catch (\Exception $e) {
			return null;
		}
	}
}