<?php

namespace App\Mail;

use App\Email;
use App\Helpers\Hashids;
use App\Subscriber;
use GuzzleHttp\Client;
use Html2Text\Html2Text;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Illuminate\Database\Eloquent\Collection;
use SparkPost\SparkPost;

class SparkyNewsletter
{
	protected $apiKey;
	protected $email;
	protected $recipients;
	protected $sender;
	protected $unsubscribeUrl;

	public function __construct(Email $email, Collection $subscribers, $sender, $unsubscribeUrl)
	{
		$this->apiKey = env('SPARKPOST_SECRET');
		$this->email = $email;
		$this->recipients = $subscribers;
		$this->sender = $sender;
		$this->unsubscribeUrl = $unsubscribeUrl;
	}

	/**
	 * Send email
	 * @return array
	 */
	public function send()
	{
		$httpClient = new GuzzleAdapter(new Client());
		$sparky = new SparkPost($httpClient, ['key' => $this->apiKey]);

		try {
			$sparky->setOptions(['async' => false, 'retries' => 3]);
			$promise = $sparky->transmissions->post($this->getSparkyContent());
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
	 * Everything except unsubscribe_link is a property of Subscriber model
	 * The value represents what the user entered in their HTML
	 * @return array
	 */
	protected function getSubstitutionVariables()
	{
		return [
			'id' => '%id%',
			'first_name' => '%first_name%',
			'last_name' => '%last_name%',
			'name' => '%name%',
			'email' => '%email%',
			'unsubscribe' => '%unsubscribe%',
//			'unsubscribe_link' => '%unsubscribe_link%',
		];
	}

	/**
	 * Replace user's substitution data with the Sparkpost format.
	 * Unescape unsubscribe link
	 * @param $content
	 * @return mixed
	 */
	protected function replaceSubstitutionVariables($content)
	{
		$substitutionVariables = $this->getSubstitutionVariables();

		if ( $substitutionVariables ) {
			foreach ( $substitutionVariables as $key => $variable ) {
				$content = ( $key === 'unsubscribe' )
					? str_ireplace($variable, "<a href='{$this->unsubscribeUrl}?unique={{ $key }}' data-msys-unsubscribe='1'>unsubscribe</a>", $content)
					: str_ireplace($variable, "{{ $key }}", $content);
			}
		}

		return $content;
	}

	/**
	 * Get substitution data for a given subscriber
	 * @param Subscriber $subscriber
	 * @return array
	 */
	protected function getSubstitutionData(Subscriber $subscriber)
	{
		$substitutionVariables = $this->getSubstitutionVariables();
		$data = [];

		if ( $substitutionVariables ) {
			foreach ( $substitutionVariables as $key => $variable ) {
				if ( $key === 'unsubscribe' )
					$data[$key] = Hashids::encode($subscriber->id);
				else
					$data[$key] = $subscriber->{$key};
			}
		}

		return $data;
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
