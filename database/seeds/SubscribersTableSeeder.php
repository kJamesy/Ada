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

	    $mLists = cache()->remember('seed-mailing-lists', 120, function() {
		    return \App\MailingList::get();
	    });

	    if ( $count = $mLists->count() ) {
	    	$subscribers->each(function($subscriber) use ($mLists, $count) {
			    $num = $count > 5 ? 5 : $count;
			    $randomMLists = $mLists->random(rand(0, $num))->pluck('id')->toArray();

	    		$subscriber->mailing_lists()->attach($randomMLists);
		    });
	    }
    }
}
