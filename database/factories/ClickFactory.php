<?php

use Faker\Generator as Faker;

$factory->define(\App\Click::class, function (Faker $faker) {
	$opens = cache()->remember('seed-opens', 120, function() {
		return \App\Open::get();
	});

	if (  $opens->count() ) {
		$open = $opens->random();

		$first_clicked_at = $open->first_opened_at->addMinutes(rand(1,3));
		$last_clicked_at = $first_clicked_at;
		$clicks = rand(1,5);

		if ( $clicks > 1 )
			$last_clicked_at = $first_clicked_at->addMinutes(rand(1,3));

		return [
			'email_id' => $open->email_id,
			'subscriber_id' => $open->subscriber_id,
			'link' => $faker->url,
			'clicks' => $clicks,
			'first_clicked_at' => $first_clicked_at,
			'last_clicked_at' => $last_clicked_at,
		];
	}

	return [];
});
