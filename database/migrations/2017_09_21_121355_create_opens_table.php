<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opens', function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('email_id')->unsigned();
	        $table->integer('subscriber_id')->unsigned();
	        $table->string('ip_address')->nullable();
	        $table->string('country')->nullable();
	        $table->string('device')->nullable();
	        $table->string('os')->nullable();
	        $table->string('browser')->nullable();
	        $table->text('user_agent')->nullable();
	        $table->integer('opens')->default(1);
	        $table->dateTime('first_opened_at');
	        $table->dateTime('last_opened_at');
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
        Schema::dropIfExists('opens');
    }
}
