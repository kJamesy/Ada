<?php

use Faker\Generator as Faker;

$factory->define(\App\Open::class, function (Faker $faker) {
	$deliveries = cache()->remember('seed-deliveries', 15, function() {
		return \App\Delivery::get();
	});

	if (  $deliveries->count() ) {
		$delivery = $deliveries->random();

		$first_opened_at = $delivery->delivered_at->addMinutes(rand(1,10));
		$last_opened_at = $first_opened_at;
		$opens = rand(1,5);

		if ( $opens > 1 )
			$last_opened_at = $first_opened_at->addMinutes(rand(5,60));

		$user_agent = $faker->userAgent;
		$country = \App\Tracking\SparkyTracking::getCountryNameFromAlpha2Code($faker->countryCode);
		$device = \App\Tracking\SparkyTracking::getDeviceNameFromUserAgent($user_agent);
		$os = \App\Tracking\SparkyTracking::getOSFromUserAgent($user_agent);
		$browser = \App\Tracking\SparkyTracking::getBrowserFromUserAgent($user_agent);

		return [
			'email_id' => $delivery->email_id,
			'subscriber_id' => $delivery->subscriber_id,
			'ip_address' => $faker->ipv4,
			'country' => $country,
			'device' => $device,
			'os' => $os,
			'browser' => $browser,
			'user_agent' => $user_agent,
			'opens' => $opens,
			'first_opened_at' => $first_opened_at,
			'last_opened_at' => $last_opened_at,
		];
	}

	return [];
});
