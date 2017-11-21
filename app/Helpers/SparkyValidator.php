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
//		$response = $sparky->syncRequest('POST', "/sending-domains/$domain/verify", ['dkim_verify' => true]);

		$promise = $sparky->request('GET', 'metrics/ip-pools', [
			'from' => '2014-12-01T09:00',
			'to' => '2015-12-01T08:00',
			'timezone' => 'America/New_York',
			'limit' => '5',
		]);
		
		try {
			dd($promise);
			return ['success' => true, 'response' => $sparky->transmissions->get()];
		}
		catch (\Exception $e) {
			dd($e->getMessage());
			return ['error' => true, 'message' => $e->getMessage()];
		}
	}
}