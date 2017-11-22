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

	public static function validateSendingDomain($domain)
	{
		$httpClient = new GuzzleAdapter(new Client());
		$sparky = new SparkPost($httpClient, ['key' => static::getApiKey()]);
		$response = $sparky->syncRequest("POST", "sending-domains/$domain/verify", ['dkim_verify' => TRUE]);

//		$sparky->setOptions(['async' => false]);
//		$response = $sparky->request('GET', 'sending-domains');

		var_dump($response->getStatusCode());
		var_dump($response->getBody());

	}
}