<?php

use Illuminate\Database\Seeder;

class DeliveriesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    $deliveries = factory(\App\Delivery::class, 20000)->create();
    }
}
