<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('user_id')->unsigned();
	        $table->integer('campaign_id')->unsigned();
	        $table->text('sender');
	        $table->string('subject')->nullable();
	        $table->mediumText('content')->nullable();
	        $table->integer('recipients_num')->nullable();
	        $table->boolean('is_deleted')->default(0);
	        $table->tinyInteger('status')->default(-2); //-2 Draft; -1 Scheduled; 0 Failed; 1 Success
	        $table->dateTime('sent_at')->nullable();
            $table->timestamps();

	        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
	        $table->foreign('campaign_id')->references('id')->on('campaigns')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('emails');
    }
}
