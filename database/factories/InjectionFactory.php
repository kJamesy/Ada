<?php

use Faker\Generator as Faker;

$factory->define(\App\Injection::class, function (Faker $faker) {

	$vars = findEligibleInjection();

	$email = $vars['email'];
	$subscriber = $vars['subscriber'];

	if (  $email && $subscriber ) {
		return [
			'email_id' => $email->id,
			'subscriber_id' => $subscriber->id,
			'injected_at' => $email->sent_at->addMinutes(rand(1,5)),
		];
	}

	return [];

});

/**
 * Find an eligible injection (one that does not already exist)
 * @return array
 */
function findEligibleInjection()
{
	$subscriber = cache()->remember('seed-subscribers', 120, function() {
		return \App\Subscriber::where('is_deleted', 0)->where('active', 1)->get();
	})->random();

	$email = cache()->remember('seed-emails', 120, function() {
		return \App\Email::where('status', 1)->get();
	})->random();

	if (  $email && $subscriber ) {
		if ( $exists = \App\Injection::findResourceBelongingTo( $email->id, $subscriber->id ) )
			return findEligibleInjection();
	}

	return compact('email', 'subscriber');
}