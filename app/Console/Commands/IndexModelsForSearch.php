<?php

namespace App\Console\Commands;

use App\Campaign;
use App\Click;
use App\Delivery;
use App\Email;
use App\EmailSetting;
use App\Failure;
use App\MailingList;
use App\Open;
use App\Subscriber;
use App\Template;
use App\User;
use Illuminate\Console\Command;

class IndexModelsForSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elasticsearch:index';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add specified models to search index';

	/**
	 * All models that need to be indexed
	 * @var
	 */
    protected $models;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->models = [
        	Campaign::class,
	        Click::class,
	        Delivery::class,
	        Email::class,
	        EmailSetting::class,
	        Failure::class,
	        MailingList::class,
	        Open::class,
	        Subscriber::class,
	        Template::class,
	        User::class
        ];
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        foreach( $this->models as $modelString ) {
	        $model = new $modelString;
	        $model->get()->searchable();
        }

	    $this->info('All done');
        return true;
    }
}
