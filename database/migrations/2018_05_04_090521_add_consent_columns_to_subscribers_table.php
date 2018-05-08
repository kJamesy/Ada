<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConsentColumnsToSubscribersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
	    Schema::table('subscribers', function (Blueprint $table) {
		    $table->tinyInteger('consent')->default(-1)->after('active'); //-1 Not Yet, 0 - Withdrawn, 1 - Consented
		    $table->dateTime('reviewed_at')->nullable()->after('consent');
	    });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
	    Schema::table('subscribers', function (Blueprint $table) {
		    $table->dropColumn('consent');
		    $table->dropColumn('reviewed_at');
	    });
    }
}
