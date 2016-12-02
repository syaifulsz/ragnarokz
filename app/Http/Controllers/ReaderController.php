<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

// Vendors
use Carbon\Carbon;

class ReaderController extends Controller
{
    /**
     * Render view for manga home page
     * @param       View::make()
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'Manga Index',
            'mangas' => []
        ];
        $data['mangas'] = \App\Manga::all();

        View::share($data);
        return view('manga.index');
    }

    /**
     * Render view for manga chapters
     * @param       {String}        $manga_slug       manga's slug
     * @param       View::make()
     */
    public function showMangaChapters(Request $request, $manga_slug)
    {
        $data = [
            'request' => $request,
            'pageTitle' => null,
            'manga' => null,
            'breadcrumb' => null,
            'recents' => null,
            'savedPhotos' => []
        ];

        // set manga
        $query = new \App\Manga();
        $manga = $query->slug($manga_slug)->first();

        if (!$manga) abort(404, 'Page not found.');

        $data['manga'] = $manga;

        // set title
        $data['pageTitle'] = $manga->_title();

        // set breadcrumb
        $this->breadcrumb["{$manga->_title()}"] = $manga->_url();
        $data['breadcrumb'] = $this->breadcrumb;

        // if recent exist
        if ($manga->recents()) {

            // set recent read chapters
            $getRecents = $manga->recents()->orderBy('created_at', 'desc')->paginate(6);
            $recentFirst = $getRecents->first();
            if ($recentFirst) {
                $data['recents']['first'] = $recentFirst->_recent();
            }
            $getRecents->shift();
            foreach ($getRecents as $recent) {
                $data['recents']['recents'][] = $recent->_recent();
            }

            // set chapter cover
            $data['chapterCover'] = $recentFirst ? $recentFirst->chapter->_cover() : null;

            // set chapters
            $data['chapters'] = $manga
                ->chapters()
                ->orderBy('manga_chapter_order', $request->get('sort'))
                ->paginate(30)
                ->appends(Input::except('page'));
        }

        View::share($data);
        return view('manga.chapters');
    }

    /**
     * Render view for manga page
     * @param       {String}        $manga_slug       manga's slug
     * @param       {String}        $chapter_slug     manga chapter's slug
     * @param       View::make()
     */
    public function showMangaPage($manga_slug, $chapter_slug)
    {
        $data = [
            'breadcrumb' => [],
            'recents' => [],
            'chapters' => [],
            'pagination' => [],
            'progressChapter' => null,
            'manga' => []
        ];

        // set chapters
        $query = new \App\MangaChapter();
        $chapter = $query->slug("{$manga_slug}-{$chapter_slug}")->first();

        if (!$chapter) abort(404, 'Page not found.');

        $data['chapter'] = $chapter ? $chapter : [];
        $data['manga'] = $chapter ? $chapter->manga : [];
        $data['pages'] = $chapter ? $chapter->pages : [];

        // init manga
        $manga = new \App\Manga();
        $manga = $manga->slug($manga_slug)->first();

        // set manga
        $data['manga'] = $manga;

        // set recent read chapters
        $recent = new \App\MangaChapterRecent();
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

        // set recents
        $getRecents = [];
        $queryRecents = $manga
            ->recents()
            ->where('manga_chapter_id', '!=', $chapter->manga_chapter_id)
            ->orderBy('created_at', 'desc')
            ->paginate(5);

        $recent_key = 0;
        foreach ($queryRecents as $recent) {
            $recentChapter = $recent->chapter;
            $getRecents[$recentChapter->_order()] = $recent;
            $recent_key++;
        }

        krsort($getRecents);

        if ($getRecents) {
            $recentFirst = array_shift($getRecents);
            $data['recents']['first'] = $recentFirst->_recent();
            foreach ($getRecents as $recent) {
                $data['recents']['recents'][] = $recent->_recent();
            }
        }

        // set progress chapter
        $totalChapters = $manga->chapters->count();
        $readChapters = $manga->chapters->where('manga_chapter_order', '<=', $chapter->_order())->count();
        $remainingChapters = $manga->chapters->where('manga_chapter_order', '>', $chapter->_order())->count();
        $progressChapter = ($readChapters/$totalChapters) * 100;
        $data['progressChapter'] = [
            'total' => $totalChapters,
            'read' => $readChapters,
            'remaining' => $remainingChapters,
            'progress' => $progressChapter
        ];

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
        $data['pagination'] = $pagination;

        // set breadcrumb
        $this->breadcrumb[$chapter->manga->manga_title] = $chapter->manga->_url();
        $this->breadcrumb[$chapter->_title()] = $chapter->_url();
        $data['breadcrumb'] = $this->breadcrumb;

        // set page title
        $data['pageTitle'] = "{$chapter->_title()} - {$chapter->manga->_title()}";

        // set chapter cover
        $data['chapterCover'] = $chapter->_cover();

        View::share($data);
        return view('manga.page');
    }
}
