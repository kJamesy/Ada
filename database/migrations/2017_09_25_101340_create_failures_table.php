<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFailuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('failures', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('email_id')->unsigned();
	        $table->integer('subscriber_id')->unsigned();
	        $table->string('type');
	        $table->text('reason')->nullable();
	        $table->integer('fails')->default(1);
	        $table->dateTime('first_failed_at');
	        $table->dateTime('last_failed_at');
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
        Schema::dropIfExists('failures');
    }
}
