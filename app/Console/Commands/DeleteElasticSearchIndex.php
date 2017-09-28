<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteElasticSearchIndex extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:delete-index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an elastic search index';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return exec("curl -XDELETE localhost:9200/" . env('ELASTICSEARCH_INDEX'));
    }
}
