<?php

namespace App\Mail;

use App\Email;
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
			'unsubscribe_text' => '%unsubscribe_text=',
			'unsubscribe_link' => '%unsubscribe_link%',
			'view_this_email_in_the_browser' => '%view_this_email_in_the_browser%',
			'view_this_email_in_the_browser_text' => '%view_this_email_in_the_browser_text=',
			'review_your_preferences' => '%review_your_preferences%',
			'review_your_preferences_text' => '%review_your_preferences_text=',
			'review_your_preferences_link' => '%review_your_preferences_link%',
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
				if ( $key === 'unsubscribe' )
					$content = str_ireplace($variable, "<a href='{$this->unsubscribeUrl}?unique={{ $key }}' data-msys-unsubscribe='1'>unsubscribe</a>", $content);

				elseif ( $key === 'unsubscribe_link' )
					$content = str_ireplace($variable, "{$this->unsubscribeUrl}?unique={{ $key }}", $content);

				elseif ( $key === 'unsubscribe_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$this->unsubscribeUrl}?unique={{ $key }}' data-msys-unsubscribe='1'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'view_this_email_in_the_browser' )
					$content = str_ireplace($variable, "<a href='{$this->viewInBrowserUrl}?unique={{ $key }}'>view this email in the browser</a>", $content);

				elseif ( $key === 'view_this_email_in_the_browser_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$this->viewInBrowserUrl}?unique={{ $key }}'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'review_your_preferences' )
					$content = str_ireplace($variable, "<a href='{$this->reviewYourPreferencesUrl}?unique={{ $key }}'>review your preferences</a>", $content);

				elseif ( $key === 'review_your_preferences_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$this->reviewYourPreferencesUrl}?unique={{ $key }}'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'review_your_preferences_link' )
					$content = str_ireplace($variable, "{$this->reviewYourPreferencesUrl}?unique={{ $key }}", $content);

				else
					$content = str_ireplace($variable, "{{ $key }}", $content);
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
				if ( $key === 'unsubscribe' || $key === 'unsubscribe_text' || $key === 'unsubscribe_link' || $key === 'view_this_email_in_the_browser'
				     || $key === 'view_this_email_in_the_browser_text' || $key === 'review_your_preferences' || $key === 'review_your_preferences_text'
				     || $key === 'review_your_preferences_link' )
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
