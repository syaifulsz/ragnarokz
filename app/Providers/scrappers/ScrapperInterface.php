<?php

namespace App\Providers\Scrappers;

interface ScrapperInterface
{

    /**
     * addManga()
     *
     * @param string    $url    manga's url
     */
    public function addManga($url = null);

    /**
     * updateMangaChapter()
     * Fetch and update chapters for specific manga
     *
     * @param  string   $slug   manga's slug
     * @return array
     */
    public function updateMangaChapter($slug);

    /**
     * updateMangaPage()
     * Fetch and update pages for specific manga chapter
     *
     * @param  string   $manga_slug     manga's slug
     * @param  string   $chapter_slug   manga chapter's slug
     * @return array
     */
    public function updateMangaPage($manga_slug, $chapter_slug);

    /**
     * saveImage()
     *
     * @param  array $options
     * @return array
     */
    public function saveImage($options);

    /**
     * saveMangaPageImages()
     *
     * @param  string   $manga_slug     manga's slug
     * @param  string   $chapter_slug   manga chapter's slug
     * @return array
     */
    public function saveMangaPageImages($manga_slug, $chapter_slug);

}
