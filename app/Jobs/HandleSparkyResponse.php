<?php

namespace App\Jobs;

use App\SparkyResponse;
use App\Tracking\SparkyTracking;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class HandleSparkyResponse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sparky_response;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SparkyResponse $sparky_response)
    {
        $this->sparky_response = $sparky_response;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
    	if ( $this->sparky_response ) {

		    if ( $request = json_decode($this->sparky_response->body) ) {
		    	$sparky_tracking = new SparkyTracking($request);
		    	$sparky_tracking->handleEvents();
		    }

	    }
    }

}
