<?php

use Faker\Generator as Faker;

$factory->define(\App\Template::class, function (Faker $faker) {
    return [
	    'name' => ucfirst($faker->unique()->words(rand(1,5), true)),
	    'description' => $faker->sentences(rand(1,3), true),
	    'content' => $faker->paragraphs(rand(5, 10), true),
	    'last_editor' => $faker->name,
	    'is_deleted' => rand(0,1),
    ];
});
