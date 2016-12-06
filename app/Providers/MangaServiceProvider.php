<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MangaServiceProvider extends ServiceProvider
{
    public function __construct()
    {
        $this->scrapper = new \App\Providers\Scrappers\Scrapper();
        $this->manager = new \App\Providers\Managers\MangaManager();
    }

    /**
     * addManga()
     *
     * @inheritDoc
     */
    public function addManga($url) {
        return $this->scrapper->addManga($url);
    }

    /**
     * updateMangaChapter()
     *
     * @inheritDoc
     */
    public function updateMangaChapter($slug)
    {
        return $this->scrapper->updateMangaChapter($slug);
    }

    /**
     * saveMangaPageImages()
     *
     * @inheritDoc
     */
    public function saveMangaPageImages($manga_slug, $chapter_slug)
    {
        return $this->scrapper->saveMangaPageImages($manga_slug, $chapter_slug);
    }

    /**
     * updateMangaPage()
     *
     * @inheritDoc
     */
    public function updateMangaPage($manga_slug, $chapter_slug)
    {
        return $this->scrapper->updateMangaPage($manga_slug, $chapter_slug);
    }

    /**
     * deleteManga()
     *
     * @inheritDoc
     */
    public function deleteManga($slug)
    {
        $this->manager->deleteManga($slug);
    }
}
