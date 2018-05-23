<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 22/05/2018
 * Time: 17:44
 */

namespace App\Helpers;


use App\Subscriber;

class EmailVariables
{

	/**
	 * Define an array of substitution variables
	 * @return array
	 */
	public static function getSubstitutionVariables()
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
			'view_this_email_in_the_browser_link' => '%view_this_email_in_the_browser_link%',
			'review_your_preferences' => '%review_your_preferences%',
			'review_your_preferences_text' => '%review_your_preferences_text=',
			'review_your_preferences_link' => '%review_your_preferences_link%',
		];
	}

	/**
	 * Replace user's substitution data with the Sparkpost format.
	 * @param $substitutionVariables
	 * @param $content
	 * @param $unsubscribeUrl
	 * @param $viewInBrowserUrl
	 * @param $reviewYourPreferencesUrl
	 *
	 * @return mixed
	 */
	public static function replaceSubstitutionVariables($substitutionVariables, $content, $unsubscribeUrl, $viewInBrowserUrl, $reviewYourPreferencesUrl)
	{
		$content = static::sanitiseHrefs($content);
		
		if ( $substitutionVariables ) {
			foreach ( $substitutionVariables as $key => $variable ) {
				if ( $key === 'unsubscribe' )
					$content = str_ireplace($variable, "<a href='{$unsubscribeUrl}?unique={{ $key }}' data-msys-unsubscribe='1'>unsubscribe</a>", $content);

				elseif ( $key === 'unsubscribe_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$unsubscribeUrl}?unique={{ $key }}' data-msys-unsubscribe='1'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'unsubscribe_link' )
					$content = str_ireplace($variable, "{$unsubscribeUrl}?unique={{ $key }}", $content);

				elseif ( $key === 'view_this_email_in_the_browser' )
					$content = str_ireplace($variable, "<a href='{$viewInBrowserUrl}?unique={{ $key }}'>view this email in the browser</a>", $content);

				elseif ( $key === 'view_this_email_in_the_browser_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$viewInBrowserUrl}?unique={{ $key }}'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'view_this_email_in_the_browser_link' )
					$content = str_ireplace($variable, "{$viewInBrowserUrl}?unique={{ $key }}", $content);

				elseif ( $key === 'review_your_preferences' )
					$content = str_ireplace($variable, "<a href='{$reviewYourPreferencesUrl}?unique={{ $key }}'>review your preferences</a>", $content);

				elseif ( $key === 'review_your_preferences_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$reviewYourPreferencesUrl}?unique={{ $key }}'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'review_your_preferences_link' )
					$content = str_ireplace($variable, "{$reviewYourPreferencesUrl}?unique={{ $key }}", $content);

				else
					$content = str_ireplace($variable, "{{ $key }}", $content);
			}
		}

		return $content;
	}


	/**
	 * Replace user's substitution data with the Sparkpost format - for use in the browser using the 'view in the browser' link
	 * @param $substitutionVariables
	 * @param $content
	 * @param $unsubscribeUrl
	 * @param $viewInBrowserUrl
	 * @param $reviewYourPreferencesUrl
	 * @param Subscriber $subscriber
	 *
	 * @return mixed
	 */
	public static function replaceSubstitutionVariablesForBrowser($substitutionVariables, $content, $unsubscribeUrl, $viewInBrowserUrl, $reviewYourPreferencesUrl, Subscriber $subscriber)
	{
		$content = static::sanitiseHrefs($content);
		$encodedSubscriberId = Hashids::encode($subscriber->id);

		if ( $substitutionVariables ) {
			foreach ( $substitutionVariables as $key => $variable ) {
				if ( $key === 'unsubscribe' )
					$content = str_ireplace($variable, "<a href='{$unsubscribeUrl}?unique={$encodedSubscriberId}' target='_blank'>unsubscribe</a>", $content);

				elseif ( $key === 'unsubscribe_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$unsubscribeUrl}?unique={$encodedSubscriberId}' target='_blank'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'unsubscribe_link' )
					$content = str_ireplace($variable, "{$unsubscribeUrl}?unique={$encodedSubscriberId}", $content);

				elseif ( $key === 'view_this_email_in_the_browser' )
					$content = str_ireplace($variable, "<a href='#'>view this email in the browser</a>", $content);

				elseif ( $key === 'view_this_email_in_the_browser_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='#'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'view_this_email_in_the_browser_link' )
					$content = str_ireplace($variable, "{$viewInBrowserUrl}?unique={$encodedSubscriberId}", $content);

				elseif ( $key === 'review_your_preferences' )
					$content = str_ireplace($variable, "<a href='{$reviewYourPreferencesUrl}?unique={$encodedSubscriberId}' target='_blank'>review your preferences</a>", $content);

				elseif ( $key === 'review_your_preferences_text' ) {
					$start = $variable;
					$end = '%';

					$occurrences = preg_split("/$start/i", $content);

					if ( count($occurrences) ) {
						foreach( $occurrences as $occurrence ) {
							if ( strlen($occurrence) ) {
								$customText = preg_split("/$end/i", $occurrence)[0];
								if ( strlen($customText) )
									$content = str_ireplace("$start$customText$end", "<a href='{$reviewYourPreferencesUrl}?unique={$encodedSubscriberId}' target='_blank'>$customText</a>", $content);
							}
						}
					}
				}

				elseif ( $key === 'review_your_preferences_link' )
					$content = str_ireplace($variable, "{$reviewYourPreferencesUrl}?unique={$encodedSubscriberId}", $content);

				else
					$content = str_ireplace( $variable, $subscriber->{$key}, $content );
			}
		}

		return $content;
	}


	/**
	 * Get substitution data for a given subscriber
	 * @param $substitutionVariables
	 * @param Subscriber $subscriber
	 *
	 * @return array
	 */
	public static function getSubstitutionData($substitutionVariables, Subscriber $subscriber)
	{
		$data = [];

		if ( $substitutionVariables ) {
			foreach ( $substitutionVariables as $key => $variable ) {
				if ( $key === 'unsubscribe' || $key === 'unsubscribe_text' || $key === 'unsubscribe_link' || $key === 'view_this_email_in_the_browser'
				     || $key === 'view_this_email_in_the_browser_text' || $key === 'view_this_email_in_the_browser_link' || $key === 'review_your_preferences'
				     || $key === 'review_your_preferences_text' || $key === 'review_your_preferences_link' )
					$data[$key] = Hashids::encode($subscriber->id);
				else
					$data[$key] = $subscriber->{$key};
			}
		}

		return $data;
	}


	/**
	 * Take HREFs and sanitise them to contain only substitution variables
	 * @param $content
	 *
	 * @return mixed|string
	 */
	public static function sanitiseHrefs($content)
	{
		try {
			if ( $content ) {
				$dom = new \DOMDocument();
				$dom->loadHTML("<html>$content</html>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
				$anchors = $dom->getElementsByTagName('a');

				if ( $anchors ) {
					foreach ( $anchors as $count => $anchor ) {
						$href = $anchor->getAttribute('href');

						if ( $href ) {
							if ( str_contains(strtolower($href), '%unsubscribe_link%') )
								$anchor->setAttribute( 'href', '%unsubscribe_link%' );

							elseif ( str_contains(strtolower($href), '%review_your_preferences_link%') )
								$anchor->setAttribute( 'href', '%review_your_preferences_link%' );

							elseif ( str_contains(strtolower($href), '%view_this_email_in_the_browser_link%') )
								$anchor->setAttribute( 'href', '%view_this_email_in_the_browser_link%' );
						}
					}
				}

				$content = $dom->saveHTML();
				$content = str_replace('<html>', '', $content);
				$content = str_replace('</html>', '', $content);
			}
		}
		catch (\Exception $e) {
			//Silence
		}

		return $content;
	}


}