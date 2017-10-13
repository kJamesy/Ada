<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInjectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('injections', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('email_id')->unsigned();
	        $table->integer('subscriber_id')->unsigned();
	        $table->dateTime('injected_at');
	        $table->timestamps();

	        $table->foreign('email_id')->references('id')->on('emails')->onDelete('cascade');
	        $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('injections');
    }
}
