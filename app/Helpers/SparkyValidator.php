<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 21/11/2017
 * Time: 16:38
 */

namespace App\Helpers;


use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use SparkPost\SparkPost;

class SparkyValidator
{
	/**
	 * SparkPost API Key
	 * @return mixed
	 */
	public static function getApiKey()
	{
		return env('SPARKPOST_SECRET');
	}

	/**
	 * Verify that the supplied domain is Ok to send
	 * @param $domain
	 *
	 * @return bool|object
	 */
	public static function validateSendingDomain($domain)
	{
		$httpClient = new GuzzleAdapter(new Client());

		try {
			$sparky = new SparkPost($httpClient, ['key' => static::getApiKey()]);
			$response = $sparky->syncRequest("GET", "sending-domains/$domain", ['dkim_verify' => TRUE])->getBody();

			if ( is_array($response) && array_key_exists('results', $response) )
				return $response['results']['status']['compliance_status'] === 'valid' && $response['results']['status']['ownership_verified'];

			return false;
		}
		catch (\Exception $e) {
			return (object) ['error' => true, 'code' => $e->getCode(), 'message' => $e->getMessage()];
		}

	}
}