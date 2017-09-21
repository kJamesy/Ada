<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClicksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clicks', function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('email_id')->unsigned();
	        $table->integer('subscriber_id')->unsigned();
	        $table->text('link');
	        $table->integer('clicks')->default(1);
	        $table->dateTime('first_clicked_at');
	        $table->dateTime('last_clicked_at');
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
        Schema::dropIfExists('clicks');
    }
}
