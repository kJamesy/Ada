<?php
/**
 * Created by PhpStorm.
 * User: Jamesy
 * Date: 13/06/2018
 * Time: 13:30
 */

namespace App\Helpers;


class Menu
{

	/**
	 * Generate HTML of a two-level menu
	 * @param $pages
	 * @param null $currentPage
	 *
	 * @return string
	 */
	public static function generateTwoLevelMenu($pages, $currentPage = null, $type = 'user_guide')
	{
		$menu = '';

		if ( count($pages) ) {
			$menu = "<ul class='navbar-nav mr-auto'>";

			$firstLevel = static::getFirstLevelPages($pages);
			$nonFirstLevel = static::getNonFirstLevelPages($pages);

			if ( count($firstLevel) ) {
				foreach ( $firstLevel as $parent ) {
					$activeClass = ( $currentPage && $parent->slug === $currentPage->slug ) ? 'active' : '';
					$menu .= "<li class='$activeClass'>";
					$menu .= "<a href='";

					if ( $type === 'developer_guide' )
						$menu .= route('guest-developer-guides.show', ['slug' => $parent->slug]);
					else
						$menu .= route('guest-user-guides.show', ['slug' => $parent->slug]);

					$menu .= "'>{$parent->title}</a>";

					if ( $parent->has_children ) {
						$children = static::getChildrenPages($parent, $nonFirstLevel);

						if ( count($children) ) {
							$menu .= " <div class='animated-toggle'><i class='icon ion-chevron-down'></i></div>";
							$menu .= "<ul class='second-level-ul'>";

							foreach ( $children as $child ) {
								$activeClass = ( $currentPage && $child->slug === $currentPage->slug ) ? 'active' : '';
								$menu .= "<li class='$activeClass'>";
								$menu .= "<a href='";

								if ( $type === 'developer_guide' )
									$menu .= route('guest-developer-guides.show', ['slug' => $child->slug]);
								else
									$menu .= route('guest-user-guides.show', ['slug' => $child->slug]);

								$menu .= "'>{$child->title}</a>";
								$menu .= "</li>";
							}

							$menu .= "</ul>";
						}
					}

					$menu .= "</li>";
				}
			}

			$menu .= "</ul>";
		}

		return $menu;
	}

	/**
	 * Get an array of all first-level pages
	 * @param $pages
	 *
	 * @return array
	 */
	protected static function getFirstLevelPages($pages)
	{
		$firstLevel = [];

		if ( count($pages) ) {
			foreach ( $pages as $page ) {
				if ( ! $page->has_parent )
					$firstLevel[] = $page;
			}
		}

		return $firstLevel;
	}

	/**
	 * Get an array of all non-first-level pages
	 * @param $pages
	 *
	 * @return array
	 */
	protected static function getNonFirstLevelPages($pages)
	{
		$nonFirstLevel = [];

		if ( count($pages) ) {
			foreach ( $pages as $page ) {
				if ( $page->has_parent )
					$nonFirstLevel[] = $page;
			}
		}

		return $nonFirstLevel;
	}

	/**
	 * Get children pages for the supplied page
	 * @param $parent
	 * @param $haystack
	 *
	 * @return array
	 */
	protected static function getChildrenPages($parent, $haystack)
	{
		$children = [];

		if ( $parent && count($haystack) ) {
			foreach ( $haystack as $child ) {
				if ( $child->parent_id === $parent->id )
					$children[] = $child;
			}
		}

		return $children;
	}
}