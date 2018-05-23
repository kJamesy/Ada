<?php

namespace App\Mail;

use App\Email;
use App\Helpers\EmailVariables;
use App\Helpers\Hashids;
use App\Subscriber;
use GuzzleHttp\Client;
use Html2Text\Html2Text;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use SparkPost\SparkPost;

class SparkyNewsletter
{
	protected $apiKey;
	protected $email;
	protected $recipients;
	protected $sender;
	protected $unsubscribeUrl;
	protected $viewInBrowserUrl;
	protected $reviewYourPreferencesUrl;

	/**
	 * SparkyNewsletter constructor.
	 *
	 * @param Email $email
	 * @param Collection $subscribers
	 * @param $sender
	 * @param $unsubscribeUrl
	 * @param $viewInBrowserUrl
	 * @param $reviewYourPreferencesUrl
	 */
	public function __construct(Email $email, Collection $subscribers, $sender, $unsubscribeUrl, $viewInBrowserUrl, $reviewYourPreferencesUrl)
	{
		$this->apiKey = env('SPARKPOST_SECRET');
		$this->email = $email;
		$this->recipients = $subscribers;
		$this->sender = $sender;
		$this->unsubscribeUrl = $unsubscribeUrl;
		$this->viewInBrowserUrl = $viewInBrowserUrl;
		$this->reviewYourPreferencesUrl = $reviewYourPreferencesUrl;
	}

	/**
	 * Send email
	 * @return array
	 */
	public function send()
	{
		$httpClient = new GuzzleAdapter(new Client());
		$sparky = new SparkPost($httpClient, ['key' => $this->apiKey]);

		$sparky->setOptions(['async' => false, 'retries' => 3]);
		$promise = $sparky->transmissions->post($this->getSparkyContent());

		try {
			return ['success' => true, 'response' => $sparky->transmissions->get()];
		}
		catch (\Exception $e) {
			return ['error' => true, 'message' => $e->getMessage()];
		}
	}

	/**
	 * Build the content for Sparkpost
	 * @return array
	 */
	protected function getSparkyContent()
	{
		$preparedContent = $this->replaceSubstitutionVariables($this->email->content);
		$html = "<html><body>$preparedContent</body></html>";
		$html2Text = new Html2Text($html);
		$text = $html2Text->getText();
		$preparedRecipients = $this->getSparkyRecipients($this->recipients);

		return [
			'campaign_id' => $this->email->campaign->name,
			'content' => [
				'from' => $this->sender,
				'reply_to' => $this->email->reply_to_email,
				'subject' => $this->email->subject,
				'html' => $html,
				'text' => $text
			],
//		    'return_path' => $this->sender['email'], // For bounces
			'recipients' => $preparedRecipients,
		];
	}

	/**
	 * Define all variables to be substituted.
	 * The value represents what the user entered in their HTML
	 * @return array
	 */
	protected function getSubstitutionVariables()
	{
		return EmailVariables::getSubstitutionVariables();
	}

	/**
	 * Replace user's substitution data with the Sparkpost format.
	 * @param $content
	 * @return mixed
	 */
	protected function replaceSubstitutionVariables($content)
	{
		return EmailVariables::replaceSubstitutionVariables($this->getSubstitutionVariables(), $content, $this->unsubscribeUrl, $this->viewInBrowserUrl, $this->reviewYourPreferencesUrl);
	}

	/**
	 * Get substitution data for a given subscriber
	 * @param Subscriber $subscriber
	 * @return array
	 */
	protected function getSubstitutionData(Subscriber $subscriber)
	{
		return EmailVariables::getSubstitutionData($this->getSubstitutionVariables(), $subscriber);
	}

	/**
	 * Get recipient address details
	 * @param Subscriber $subscriber
	 * @return array
	 */
	protected function getRecipientAddress(Subscriber $subscriber)
	{
		return [
			'name' => "{$subscriber->first_name} {$subscriber->last_name}",
			'email' => $subscriber->email
		];
	}

	/**
	 * Get an array of Sparkpost recipients
	 * @param Collection $subscribers
	 * @return array
	 */
	protected function getSparkyRecipients(Collection $subscribers)
	{
		$recipients = [];

		if ( $subscribers ) {
			foreach ( $subscribers as $subscriber ) {
				$recipients[] = [
					'address' => $this->getRecipientAddress($subscriber),
					'substitution_data' => $this->getSubstitutionData($subscriber),
					'metadata' => [
						'subscriber_id' => $subscriber->id,
						'email_id' => $this->email->id
					]
				];
			}
		}

		return $recipients;
	}

}
