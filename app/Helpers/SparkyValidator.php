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
	 * Sparkpost API EndPoint
	 * @return string
	 */
	public static function getApiEndPoint()
	{
		return 'https://api.sparkpost.com/api/v1';
	}

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
//		$httpClient = new GuzzleAdapter(new Client());
//		$sparky = new SparkPost($httpClient, ['key' => static::getApiKey()]);
//		$response = $sparky->syncRequest("POST", "sending-domains/$domain/verify", ['dkim_verify' => TRUE]);
//
//		var_dump($response->getStatusCode());
//		var_dump($response->getBody());

		$json_submission = json_encode(['dkim_verify' => TRUE]);
		$curl = curl_init(static::getApiEndPoint() . "/sending-domains/$domain/verify");
//		$curl = curl_init(static::getApiEndPoint() . "/sending-domains/$domain");
		curl_setopt($curl, CURLOPT_HTTPHEADER,['Authorization: ' . static::getApiKey()]);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $json_submission);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);

		dd($response);
	}
}