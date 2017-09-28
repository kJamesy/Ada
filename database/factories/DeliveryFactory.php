<?php

use Faker\Generator as Faker;

$factory->define(\App\Delivery::class, function (Faker $faker) {

	$deliverable_emails = cache()->remember('seed-emails', 120, function() {
		return \App\Email::where('status', 1)->get();
	});

	$subscribers = cache()->remember('seed-subscribers', 120, function() {
		return \App\Subscriber::where('is_deleted', 0)->where('active', 1)->get();
	});

	if (  $deliverable_emails->count() && $subscribers->count() ) {
		$email = $deliverable_emails->random();
		$subscriber = $subscribers->random();

		return [
			'email_id' => $email->id,
			'subscriber_id' => $subscriber->id,
			'delivered_at' => $email->sent_at->addMinutes(rand(1,10)),
		];
	}

	return [];
});
