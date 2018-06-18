<?php

namespace App\Http\Controllers\Guest;

use App\DeveloperGuide;
use App\Helpers\Content;
use App\Helpers\Menu;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeveloperGuideController extends Controller
{

	protected $homeSlugs;

	/**
	 * DeveloperGuideController constructor.
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
		if ( ! $page = DeveloperGuide::getHomePage() ) {
			$pages = DeveloperGuide::getCachedPages();
			$menu = Menu::generateTwoLevelMenu($pages, $page, 'developer_guide');

			$replaced = new Content($page->content);
			$content = $replaced->setH2sIdAttribute();
			$anchorMenu = $replaced->getAnchorsMenu();
			$isHome = true;

			return view('guest.view-developer-guide', compact('page', 'content', 'menu', 'anchorMenu', 'isHome'));
		}

		return view('guest.404-developer-guide');
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
		if ( $page = DeveloperGuide::getPageBySlug($slug) ) {
			$pages = DeveloperGuide::getCachedPages();
			$menu = Menu::generateTwoLevelMenu($pages, $page, 'developer_guide');

			$replaced = new Content($page->content);
			$content = $replaced->setH2sIdAttribute();
			$anchorMenu = $replaced->getAnchorsMenu();
			$isHome = in_array($page->slug, $this->homeSlugs);

			return view('guest.view-developer-guide', compact('page', 'content', 'menu', 'anchorMenu', 'isHome'));
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
		$results = DeveloperGuide::getSearchResults($search, 0);

		$pages = DeveloperGuide::getCachedPages();
		$menu = Menu::generateTwoLevelMenu($pages, null, 'developer_guide');

		return view('guest.search-developer-guides', compact('results', 'menu', 'originalSearch'));
	}


}
