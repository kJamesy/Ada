<?php

use Faker\Generator as Faker;

$factory->define(\App\Email::class, function (Faker $faker) {
	$status = rand(-2,1);
	$sent_at = null;

	switch( $status ) :
		case -1 :
			$sent_at = \Carbon\Carbon::now()->addMinutes(rand(60, 90));
			break;
		case 0 :
		case 1 :
			$sent_at = \Carbon\Carbon::now()->addMinutes(rand(5,30));
			break;
	endswitch;

	$campaigns = cache()->remember('seed-campaigns', 120, function() {
		return \App\Campaign::where('is_deleted', 0)->get();
	});

	$users = cache()->remember('seed-users', 120, function() {
		return \App\User::where('active', 1)->get();
	});

	if (  $campaigns->count() && $users->count() ) {
		$campaign = $campaigns->random();
		$user = $users->random();

		return [
			'user_id'        => $user->id,
			'campaign_id'    => $campaign->id,
			'sender'         => "{$faker->name}<" . strtolower($faker->firstName). "@acw2.uk>",
			'reply_to_email' => $faker->safeEmail,
			'subject'        => $faker->sentence( rand( 1, 10 ) ),
			'content'        => $faker->text( rand( 512, 10240 ) ),
			'recipients_num' => rand( 2, 8000 ),
			'is_deleted'     => rand( 0, 1 ),
			'status'         => $status,
			'sent_at'        => $sent_at,
		];
	}

	return [];
});
