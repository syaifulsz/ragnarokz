<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use DiDom\Document;
use DiDom\Query;

class ApiController extends ScrapperController
{
    public function latestManga()
    {
        return response($this->getLatestManga());
    }

    public function mangaChapter($mangaSlug)
    {
        return response($this->getMangaChapter($mangaSlug));
    }

    public function mangaPage($mangaSlug, $mangaChapterSlug)
    {
        return response($this->getMangaPage($mangaSlug, $mangaChapterSlug));
    }

    public function scrapMangaPageImages($mangaSlug, $mangaChapterSlug)
    {
        return response($this->scrapMangaPageImages($mangaSlug, $mangaChapterSlug));
    }
}
