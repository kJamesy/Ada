<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 22/05/2018
 * Time: 17:44
 */

namespace App\Helpers;


class EmailVariables
{

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