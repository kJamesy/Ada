<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_guides', function (Blueprint $table) {
            $table->increments('id');
	        $table->integer('parent_id')->unsigned()->nullable();
	        $table->string('title');
	        $table->string('slug', 512)->unique();
	        $table->text('content');
	        $table->text('last_editor');
	        $table->boolean('is_deleted')->default(0);
	        $table->smallInteger('order')->unsigned();
            $table->timestamps();

	        $table->foreign('parent_id')->references('id')->on('user_guides')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_guides');
    }
}
