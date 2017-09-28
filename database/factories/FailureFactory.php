<?php

use Faker\Generator as Faker;

$factory->define(\App\Failure::class, function (Faker $faker) {
	$failure_types = [
		'bounce',
		'spam_complaint',
		'out_of_band',
		'policy_rejection',
		'delay',
		'generation_failure',
		'generation_rejection',
		'list_unsubscribe',
		'link_unsubscribe',
	];

	$failure_type = $failure_types[array_rand($failure_types)];
	$email = null;
	$related = null;

	switch( $failure_type ) :
		case 'spam_complaint':
			$related = \App\Open::with('email')->inRandomOrder()->first();
			$email = $related->email;
			break;
		case 'list_unsubscribe':
		case 'link_unsubscribe':
			$related = \App\Click::with('email')->inRandomOrder()->first();
			$email = $related->email;
			break;
		default:
			$email = \App\Email::where('status', 0)->whereDoesntHave('deliveries')->inRandomOrder()->first();
			break;
	endswitch;

	if ( $email ) {
		if ( $related ) {
			$first_failed_at = $related->created_at->addMinutes( rand( 1, 3 ) );
			$last_failed_at  = $first_failed_at;
			$fails           = rand( 1, 5 );

			if ( $fails > 1 ) {
				$last_failed_at = $first_failed_at->addMinutes( rand( 1, 3 ) );
			}

			return [
				'email_id'        => $email->id,
				'subscriber_id'   => $related->subscriber_id,
				'type'            => ucfirst( str_replace( '_', ' ', $failure_type ) ),
				'fails'           => $fails,
				'first_failed_at' => $first_failed_at,
				'last_failed_at'  => $last_failed_at,
			];
		}

		else {

			$subscriber = \App\Subscriber::where('is_deleted', 0)->where('active', 1)->inRandomOrder()->first();

			if ( $subscriber ) {
				$reasons = [
					"Ut est neque sint saepe dolorem nesciunt.",
					"Iusto nihil voluptatum nostrum porro temporibus.",
					"Et non eveniet nemo.",
					"Magni itaque consectetur veritatis dolorem sunt.",
					"Iusto aut est laborum delectus eum.",
					"Asperiores earum fugit voluptates at quisquam et.",
					"Blanditiis possimus sint nesciunt.",
					"Nihil repudiandae voluptate consequuntur id ipsa aut.",
					"Quos provident eum fugit similique ut rerum.",
					"Molestias ea sit et adipisci explicabo et.",
					"Omnis quasi quia aut delectus.",
					"Doloremque sed enim et est.",
				];

				$reason = array_random($reasons);
				$failed_at = $email->sent_at->addMinutes(rand(1,5));

				return [
					'email_id'        => $email->id,
					'subscriber_id'   => $subscriber->id,
					'type'            => ucfirst( str_replace( '_', ' ', $failure_type ) ),
					'reason'          => $reason,
					'first_failed_at' => $failed_at,
					'last_failed_at'  => $failed_at,
				];
			}
		}
	}

    return [
        //
    ];
});
