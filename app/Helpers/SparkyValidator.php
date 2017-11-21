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
		$sparky->setOptions(['async' => false, 'retries' => 3]);

		$promise = $sparky->request('POST', "sending-domains/$domain/verify", ['dkim_verify' => true]);

		try {
			dd($sparky->transmissions->get());
			return ['success' => true, 'response' => $sparky->transmissions->get()];
		}
		catch (\Exception $e) {
			return ['error' => true, 'message' => $e->getMessage()];
		}
	}
}