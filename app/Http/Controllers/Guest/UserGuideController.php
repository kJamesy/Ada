<?php

namespace App\Http\Controllers\Guest;

use App\Helpers\Content;
use App\Helpers\Menu;
use App\UserGuide;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserGuideController extends Controller
{
	protected $homeSlugs;

	/**
	 * UserGuideController constructor.
	 */
	public function __construct()
	{
		$this->homeSlugs = ['home-page', 'home', 'index'];
	}

	/**
	 * Display landing page
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function index()
	{
		if ( $page = UserGuide::getHomePage() ) {
			$pages = UserGuide::getCachedPages();
			$menu = Menu::generateTwoLevelMenu($pages, $page);

			$replaced = new Content($page->content);
			$content = $replaced->setH2sIdAttribute();
			$anchorMenu = $replaced->getAnchorsMenu();
			$isHome = true;

			return view('guest.view-user-guide', compact('page', 'content', 'menu', 'anchorMenu', 'isHome'));
		}

		return view('guest.404-user-guide');
	}

	/**
	 * Show page specified by slug
	 * @param Request $request
	 * @param $slug
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|mixed
	 */
	public function show(Request $request, $slug)
	{
		if ( $page = UserGuide::getPageBySlug($slug) ) {
			$pages = UserGuide::getCachedPages();
			$menu = Menu::generateTwoLevelMenu($pages, $page);

			$replaced = new Content($page->content);
			$content = $replaced->setH2sIdAttribute();
			$anchorMenu = $replaced->getAnchorsMenu();
			$isHome = in_array($page->slug, $this->homeSlugs);

			return view('guest.view-user-guide', compact('page', 'content', 'menu', 'anchorMenu', 'isHome'));
		}

		return app()->abort(404);
	}

	/**
	 * Show search results
	 * @param Request $request
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function search(Request $request)
	{
		$originalSearch = $request->search;
		$search = strtolower($originalSearch);
		$results = UserGuide::getSearchResults($search, 0);

		$pages = UserGuide::getCachedPages();
		$menu = Menu::generateTwoLevelMenu($pages);

		return view('guest.search-user-guides', compact('results', 'menu', 'originalSearch'));
	}

}
