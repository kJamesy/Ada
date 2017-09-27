<?php

use Faker\Generator as Faker;

$factory->define(\App\Subscriber::class, function (Faker $faker) {
    return [
	    'first_name' => $faker->firstName,
	    'last_name' => $faker->lastName,
	    'email' => $faker->unique()->safeEmail,
	    'active' => rand(0,1),
	    'is_deleted' => rand(0,1),
    ];
});
