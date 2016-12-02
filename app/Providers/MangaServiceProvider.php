<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class MangaServiceProvider extends ServiceProvider
{
    public function __construct()
    {
        $this->reader = new \App\Providers\Readers\Scrapper();
        $this->manager = new \App\Providers\Managers\MangaManager();
    }

    /**
     * addManga()
     *
     * @inheritDoc
     */
    public function addManga($url) {
        return $this->reader->addManga($url);
    }

    /**
     * updateMangaChapter()
     *
     * @inheritDoc
     */
    public function updateMangaChapter($slug)
    {
        return $this->reader->updateMangaChapter($slug);
    }

    /**
     * saveMangaPageImages()
     *
     * @inheritDoc
     */
    public function saveMangaPageImages($manga_slug, $chapter_slug)
    {
        return $this->reader->saveMangaPageImages($manga_slug, $chapter_slug);
    }

    /**
     * updateMangaPage()
     *
     * @inheritDoc
     */
    public function updateMangaPage($manga_slug, $chapter_slug)
    {
        return $this->reader->updateMangaPage($manga_slug, $chapter_slug);
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
