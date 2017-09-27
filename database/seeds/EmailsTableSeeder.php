<?php

use Illuminate\Database\Seeder;

class EmailsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $emails = factory(\App\Email::class, 150)->create();
    }
}
