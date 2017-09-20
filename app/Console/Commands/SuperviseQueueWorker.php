<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SuperviseQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervise:queue-worker';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Keep an eye on the queue worker. If it\'s dead, start it.';

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
	    Storage::disk('local')->put('worker_pid.txt', Carbon::now()->toDateTimeString());
    }
}
