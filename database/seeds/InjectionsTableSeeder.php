<?php

use Illuminate\Database\Seeder;

class InjectionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $injections = factory(\App\Injection::class, 35000)->create();
    }
}
