<?php

use Illuminate\Database\Seeder;

class OpensTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $opens = factory(\App\Open::class, 30000)->create();
    }
}
