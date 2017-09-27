<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TruncateSeededTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:truncate-seeded';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Truncate the tables that were seeded with dummy data.';

	/**
	 * Database tables
	 * @var array
	 */
    protected $tables;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->tables = [
        	'failures',
	        'clicks',
	        'opens',
	        'deliveries',
	        'emails',
	        'campaigns',
	        'subscribers',
	        'mailing_lists',
	        'mailing_list_subscriber',
	        'templates',
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->clearCache();

	    DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach( $this->tables as $table )
	        DB::table($table)->truncate();

	    DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        return true;
    }

	/**
	 * Clear the seeding cache
	 */
    protected function clearCache()
    {
    	cache()->forget('seed-mailing-lists');
    	cache()->forget('seed-opens');
    	cache()->forget('seed-emails');
    	cache()->forget('seed-subscribers');
    	cache()->forget('seed-campaigns');
    	cache()->forget('seed-users');
    	cache()->forget('seed-deliveries');
    }
}
