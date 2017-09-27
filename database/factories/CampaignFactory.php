<?php

use Faker\Generator as Faker;

$factory->define(App\Campaign::class, function (Faker $faker) {
	return [
		'name' => ucfirst($faker->unique()->words(rand(1,5), true)),
		'description' => $faker->sentences(rand(1,3), true),
		'is_deleted' => rand(0,1),
	];
});
