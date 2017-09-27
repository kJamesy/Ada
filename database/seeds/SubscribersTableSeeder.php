<?php

use Illuminate\Database\Seeder;

class SubscribersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $subscribers = factory(\App\Subscriber::class, 15000)->create();

	    $mLists = cache()->remember('seed-mailing-lists', 15, function() {
		    return \App\MailingList::get();
	    });

	    if ( $count = $mLists->count() ) {
		    $num = $count > 5 ? 5 : $count;
	    	$randomMLists = $mLists->random(rand(1, $num))->pluck('id')->toArray();

	    	$subscribers->each(function($subscriber) use ($randomMLists) {
	    		$subscriber->mailing_lists()->attach($randomMLists);
		    });
	    }
    }
}
