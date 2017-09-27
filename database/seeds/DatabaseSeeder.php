<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Artisan::call('db:truncate-seeded');

//         $this->call(UsersTableSeeder::class);
         $this->call(TemplatesTableSeeder::class);
         $this->call(MailingListsTableSeeder::class);
         $this->call(SubscribersTableSeeder::class);
         $this->call(CampaignsTableSeeder::class);
         $this->call(EmailsTableSeeder::class);
         $this->call(DeliveriesTableSeeder::class);
         $this->call(OpensTableSeeder::class);
         $this->call(ClicksTableSeeder::class);
         $this->call(FailuresTableSeeder::class);
    }
}
