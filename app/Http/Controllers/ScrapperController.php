<?php

namespace App\Http\Controllers;

use App\Http\Requests;

// Vendors
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use Cocur\Slugify\Slugify;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Input;

// Models
use App\Manga;
use App\MangaChapter;
use App\MangaPage;
use App\MangaPhoto;
use App\Source;

class ScrapperController extends Controller
{

    private $mangaSource = 'mangafox';

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangas
     * @api                {get} / all mangas
     * @apiDescription     returns json data for all available mangas
     */
    public function mangas()
    {
        return Manga::paginate(10);
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaChapters
     * @api                {get} /:manga_slug show specific manga
     * @apiDescription     returns json data for all chapters of a specific manga
     *
     * @param              $manga_slug manga slug
     */
    public function mangaChapters($manga_slug)
    {
        $manga = Manga::slug($manga_slug)->first();

        if (!$manga) abort(404, 'Manga not found.');

        return [
            'manga' => $manga->toArray(),
            'chapters' => $manga->chapters()->paginate(10)
        ];
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaPages
     * @api                {get} /:manga_slug/:chapter_slug show pages
     * @apiDescription     show pages that belongs to a chapter and manga
     *
     * @param              {String} $manga_slug
     * @param              {String} $chapter_slug
     * @return             {Array}
     */
    public function mangaPages($manga_slug, $chapter_slug)
    {
        $chapter = MangaChapter::slug("{$manga_slug}-{$chapter_slug}")->first();

        if (!$chapter) abort(404, 'Chapter not found.');

        return [
            'manga' => $chapter->manga->toArray(),
            'chapter' => $chapter->toArray(),
            'pages' => $chapter->pages
        ];
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaPageSave
     * @api                {post} /:manga_slug/:chapter_slug/save save page images
     * @apiDescription     scrap, delete and update page for specific chapter and manga
     *
     * @param              {String} $manga_slug manga slug
     * @param              {String} $chapter_slug manga chapter slug
     * @return             {Array}
     */
    public function mangaPageSave($mangaSlug, $mangaChapter)
    {
        $service = new \App\Providers\MangaServiceProvider();
        return $service->saveMangaPageImages($mangaSlug, $mangaChapter);
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaDelete
     * @api                {post} /:manga_slug/ delete a manga
     * @apiDescription     delete manga and all chapter and page that belongs to it
     *
     * @param              {String} $slug manga slug
     */
    public function mangaDelete($slug)
    {
        $service = new \App\Providers\MangaServiceProvider();
        return $service->deleteManga($slug);
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaAdd
     * @api                {post} /add add manga
     * @apiParam           {String} manga_url example: http://mangafox.me/manga/onepunch_man/
     * @apiDescription     scrap manga and all its chapter and register them to the database
     *
     * @param              {String} $url manga url
     */
    public function mangaAdd($url = null)
    {
        $url = $url ? $url : Input::get('manga_url');

        $service = new \App\Providers\MangaServiceProvider();
        return $service->addManga($url);
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaChapterUpdate
     * @api                {post} /:manga_slug/update update chapter
     * @apiDescription     scrap and update latest chapters for specific manga
     */
    public function mangaChapterUpdate($slug)
    {
        $service = new \App\Providers\MangaServiceProvider();
        return $service->updateMangaChapter($slug);
    }

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaPageUpdate
     * @api                {post} /:manga_slug/:chapter_slug/update update pages
     * @apiDescription     scrap, delete and update page for specific chapter and manga
     *
     * @param              {String} $manga_slug manga slug
     * @param              {String} $chapter_slug manga chapter slug
     * @return             {Array}
     */
    public function mangaPageUpdate($manga_slug, $chapter_slug)
    {
        $service = new \App\Providers\MangaServiceProvider();
        return $service->updateMangaPage($manga_slug, $chapter_slug);
    }
}
