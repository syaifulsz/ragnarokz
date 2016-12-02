<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        $query = new \App\MangaChapter();
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
}
