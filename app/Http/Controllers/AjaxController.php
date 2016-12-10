<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// Vendors
use Carbon\Carbon;

class AjaxController extends Controller
{
    /**
     * Show sneak preview or teaser for chapter pages
     *
     * @param       {String}        $manga_slug       manga's slug
     * @param       {String}        $chapter_slug     manga chapter's slug
     * @param       Respond::json()
     */
    public function chapterTeaser(Request $request)
    {
        $manga_slug = $request->has('manga_slug') ? $request->get('manga_slug') : null;
        $chapter_slug = $request->has('chapter_slug') ? $request->get('chapter_slug') : null;
        $html = $request->has('html') && ($request->get('html') == 'true' || $request->get('html') == 1) ? true  : false;

        if (!$manga_slug || !$chapter_slug) return response([], 405);

        $pages = [];
        $query = new \App\Manga\Chapter();
        $chapter = $query->slug("{$manga_slug}-{$chapter_slug}")->first();

        $pagesQuery = $chapter
            ->pages()
            ->where('manga_page_order', '>', 2)
            ->where('manga_page_order', '<', ($chapter->pages->count() - 2))
            ->get();
        $pages = $pagesQuery->count() ? $pagesQuery : [];

        if ($html) $html = view('manga._page_loop', ['pages' => $pages]);

        return $html ? $html : $pages;
    }

    public function getPage(Request $request)
    {
        $manga_slug = $request->has('manga_slug') ? $request->get('manga_slug') : null;
        $chapter_slug = $request->has('chapter_slug') ? $request->get('chapter_slug') : null;
        $html = $request->has('html') && ($request->get('html') == 'true' || $request->get('html') == 1) ? true  : false;

        if (!$manga_slug || !$chapter_slug) return response([], 405);

        $pages = [];
        $query = new \App\Manga\Chapter();
        $chapter = $query->slug("{$manga_slug}-{$chapter_slug}")->first();

        $pages = $chapter->pages;

        // init manga
        $manga = new \App\Manga\Manga();
        $manga = $manga->slug($manga_slug)->first();

        // set manga
        $data['manga'] = $manga;

        // query chapter for pagination
        $navQuery = $manga->chapters
            ->where('manga_chapter_order', '>=', ((int)$chapter->manga_chapter_order - 1))
            ->where('manga_chapter_order', '<=', ((int)$chapter->manga_chapter_order + 1))
            ->sortBy('manga_chapter_order');

        // create pagination
        $pagination = [];
        $navs = [];
        foreach ($navQuery as $nav) {
            $navs[] = $nav;
        }

        foreach ($navs as $nav_key => $nav) {
            if ($nav->manga_chapter_order == $chapter->manga_chapter_order) $current_nav_key = $nav_key;
        }

        // set pagination
        $pagination['next'] = isset($navs[$current_nav_key + 1]) ? $navs[$current_nav_key + 1] : null;
        $pagination['prev'] = $current_nav_key - 1 >= 0 ? $navs[$current_nav_key - 1] : null;

        // set breadcrumb
        $this->breadcrumb[$chapter->manga->manga_title] = $chapter->manga->_url();
        $this->breadcrumb[$chapter->_title()] = $chapter->_url();

        if ($html) {
            $html = view('manga._page_loop', ['pages' => $pages, 'pagination' => $pagination]);
            $html .= view('breadcrumb', ['breadcrumb' => $this->breadcrumb]);
        }

        return $html ? $html : $pages;
    }

    public function setRecent(Request $request)
    {
        $manga_slug = $request->has('manga_slug') ? $request->get('manga_slug') : null;
        $chapter_slug = $request->has('chapter_slug') ? $request->get('chapter_slug') : null;

        $query = new \App\Manga\Chapter();
        $chapter = $query->slug("{$manga_slug}-{$chapter_slug}")->first();

        if (!$manga_slug || !$chapter_slug) return response([], 405);

        $manga = $chapter->manga;

        // set recent read chapters
        $recent = new \App\Manga\ChapterRecent();
        $latestRecent = $recent
            ->where('manga_id', $manga->manga_id)
            ->where('created_at', '>', Carbon::now()->subHour(1))
            ->where('created_at', '<', Carbon::now())
            ->where('manga_chapter_id', $chapter->manga_chapter_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestRecent) {

            $recent->create([
                'manga_id' => $chapter->manga->manga_id,
                'manga_chapter_id' => $chapter->manga_chapter_id
            ]);
        }

        return $request->all();
    }
}
