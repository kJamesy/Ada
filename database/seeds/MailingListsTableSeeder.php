<?php

use Illuminate\Database\Seeder;

class MailingListsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $mailing_lists = factory(\App\MailingList::class, 100)->create();
    }
}
