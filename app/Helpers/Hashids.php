<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 30/04/2018
 * Time: 16:53
 */

namespace App\Helpers;


class Hashids
{

	/**
	 * Initialise hashId
	 * @return \Hashids\Hashids
	 */
	protected static function ourHashId()
	{
		return new \Hashids\Hashids('Tulip', 8, 'abcdefghijklmnopqrstuvwxyz0123456789');
	}

	/**
	 * Encode supplied string
	 * @param $input
	 *
	 * @return string
	 */
	public static function encode($input)
	{
		return static::ourHashId()->encode($input);
	}

	/**
	 * Decode supplied encoded string
	 * @param $input
	 *
	 * @return mixed
	 */
	public static function decode($input)
	{
		$output = static::ourHashId()->decode($input);
		return ( is_array($output) && count($output) ) ? $output[0] : $input;
	}

}