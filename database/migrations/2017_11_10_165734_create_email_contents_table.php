<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_contents', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('user_id')->unsigned();
	        $table->string('title', 255);
	        $table->string('slug', 512)->unique();
	        $table->mediumText('content')->nullable();
	        $table->boolean('is_deleted')->default(0);
            $table->timestamps();

	        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_contents');
    }
}
