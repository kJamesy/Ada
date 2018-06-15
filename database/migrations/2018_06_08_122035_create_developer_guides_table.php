<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeveloperGuidesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('developer_guides', function (Blueprint $table) {
	        $table->increments('id');
	        $table->integer('parent_id')->unsigned()->nullable();
	        $table->string('title');
	        $table->string('slug', 512)->unique();
	        $table->text('content');
	        $table->text('last_editor');
	        $table->boolean('is_deleted')->default(0);
	        $table->smallInteger('order')->unsigned();
	        $table->timestamps();

	        $table->foreign('parent_id')->references('id')->on('developer_guides')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('developer_guides');
    }
}
