<?php

use Illuminate\Database\Seeder;

class ClicksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $clicks = factory(\App\Click::class, 10000)->create();
    }
}
