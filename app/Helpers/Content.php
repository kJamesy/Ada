<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 14/11/2017
 * Time: 14:33
 */

namespace App\Helpers;


use Illuminate\Support\Str;

class Content
{
	protected $content;
	protected $menu;

	/**
	 * Content constructor.
	 *
	 * @param $content
	 */
	public function __construct($content)
	{
		$this->content = $content;
		$this->menu = [];
	}

	/**
	 * Add a dynamic id attribute to all H2s
	 * @return mixed|string
	 */
	public function setH2sIdAttribute()
	{
		$content = $this->content;

		try {
			if ( $this->content ) {
				$dom = new \DOMDocument();
				$dom->loadHTML("<html>$this->content</html>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
				$h2s = $dom->getElementsByTagName('h2');

				foreach ( $h2s as $count => $h2 ) {
					if ( $h2->nodeValue ) {
						$h2_slug = str_slug( Str::words( $h2->nodeValue, 12 ) ) . "-$count";
						$h2->setAttribute( 'id', $h2_slug );
						$this->menu[] = "<a href='#$h2_slug' class='nav-link'>{$h2->nodeValue}</a>";
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

	/**
	 * Get an array of menu items
	 * @return array
	 */
	public function getAnchorsMenu()
	{
		return $this->menu;
	}

}