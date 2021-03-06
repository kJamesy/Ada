<?php

use Faker\Generator as Faker;

$factory->define(\App\Delivery::class, function (Faker $faker) {

	$vars = findEligibleDelivery();
	$email = $vars['email'];
	$subscriber = $vars['subscriber'];

	if (  $email && $subscriber ) {
		return [
			'email_id' => $email->id,
			'subscriber_id' => $subscriber->id,
			'delivered_at' => $email->sent_at->addMinutes(rand(5,10)),
		];
	}

	return [];

});

/**
 * Find an eligible delivery (one that has an injection and does not already exist)
 * @return array
 */
function findEligibleDelivery()
{
	$subscriber = cache()->remember('seed-subscribers', 120, function() {
		return \App\Subscriber::where('is_deleted', 0)->where('active', 1)->get();
	})->random();

	$email = cache()->remember('seed-emails', 120, function() {
		return \App\Email::where('status', 1)->get();
	})->random();

	if (  $email && $subscriber ) {
		if ( $injectionExists = \App\Injection::findResourceBelongingTo( $email->id, $subscriber->id ) ) {
			if ( $exists = \App\Delivery::findResourceBelongingTo( $email->id, $subscriber->id ) )
				return findEligibleInjection();
		}
		else
			return findEligibleInjection();
	}

	return compact('email', 'subscriber');
}
