<?php

namespace App\Providers\Managers;

interface MangaManagerInterface
{

    /**
     * processMangaChapter()
     *
     * @param  array $source
     * @param  array $manga
     * @return array
     */
    public function processMangaChapter($source, $manga);

    /**
     * getSource()
     *
     * @param  string $url      manga's chapter url
     * @param  array $args      additional arguments
     * @return array
     */
    public function getSource($url, $args = [], $cache = true);

    /**
     * saveManga()
     *
     * @param  array $data
     * @return array
     */
    public function saveManga($data);

    /**
     * saveMangaChapter()
     *
     * @param  array $data
     * @return array
     */
    public function saveMangaChapter($data);

    /**
     * saveMangaPage()
     *
     * @param  array $data
     * @return array
     */
    public function saveMangaPage($data);

    /**
     * saveSource()
     *
     * @param  string $sourceSlug
     * @return array
     */
    public function saveSource($sourceSlug);

    /**
     * deleteManga()
     *
     * @param  string $slug
     * @return array
     */
    public function deleteManga($slug);
}
