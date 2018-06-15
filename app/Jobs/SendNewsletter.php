<?php

namespace App\Jobs;

use App\Email;
use App\Helpers\Hashids;
use App\Mail\SparkyNewsletter;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;

class SendNewsletter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

	/**
	 * The number of times the job may be attempted.
	 * @var int
	 */
	public $tries = 5;

	/**
	 * The number of seconds the job can run before timing out.
	 * @var int
	 */
	public $timeout = 180;

    protected $email;
    protected $recipients;
    protected $sender;
    protected $unsubscribeUrl;
    protected $viewInBrowserUrl;
    protected $reviewYourPreferencesUrl;

	/**
	 * Create a new job instance
	 * @param Email $email
	 * @param Collection $recipients
	 * @param $sender
	 */
	public function __construct(Email $email, Collection $recipients, $sender)
	{
		$this->email = $email;
		$this->recipients = $recipients;
		$this->sender = $sender;
		$this->unsubscribeUrl = route('subscriber.unsubscribe');
		$this->viewInBrowserUrl = route('guest-emails.display', ['id' => Hashids::encode($email->id)]);
		$this->reviewYourPreferencesUrl = route('subscriber.review');
	}

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ( $this->email && $this->recipients && $this->sender ) {
        	$sparkyNewsletter = new SparkyNewsletter($this->email, $this->recipients, $this->sender, $this->unsubscribeUrl, $this->viewInBrowserUrl, $this->reviewYourPreferencesUrl);
        	$feedback = $sparkyNewsletter->send();

        	if ( is_array($feedback) && array_key_exists('success', $feedback) )
        		$this->email->status = 1;
        	else {
//        		Mail::to($this->sender)->subject('An Error was encountered');

		        $this->email->status = 0;
	        }

        	$this->email->save();
        }
    }

	/**
	 * The job failed to process.
	 *
	 * @param  \Exception  $exception
	 * @return void
	 */
	public function failed(\Exception $exception)
	{
		$this->email->status = 0;
		$this->email->save();
	}

}
