<?php

use Illuminate\Database\Seeder;

class FailuresTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $failures = factory(\App\Failure::class, 100)->create();
    }
}
