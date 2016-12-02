<?php

namespace App\Providers\Readers;

// Facedes
use Illuminate\Support\Facades\Input;

// Vendors
use DiDom\Document;

// Components
use MangaHelper;

// Models
use App\Manga;
use App\MangaChapter;
use App\MangaPage;

class Scrapper implements ScrapperInterface
{
    public function __construct()
    {
        $this->manager = new \App\Providers\Managers\MangaManager();
    }

    protected $mangaSource = 'mangafox';

    /**
     * addManga()
     *
     * @inheritDoc
     */
    public function addManga($mangaUrl = null)
    {
        // require variables
        $chapters = [];
        $mangaTitle = null;
        $mangaSlug = null;
        $mangaUrl = $mangaUrl ? $mangaUrl : Input::get('manga_url');
        $errors = [];

        // kill process if manga_url is missing
        if (!$mangaUrl) abort(400, 'Invalid arguments.');

        $source = $this->manager->getSource($mangaUrl, [], false);

        // kill process if source return false
        if (!$source['status']) abort(400, $source['error']);

        $doc = new Document($source['content']);
        $mangaTitle = $doc->has('#series_info .cover img') ? $doc->find('#series_info .cover img')[0]->getAttribute('alt') : null;
        $mangaSlug = $mangaTitle ? MangaHelper::slugify($mangaTitle) : null;

        // saving manga
        $manga = $this->manager->saveManga([
            'manga_title' => $mangaTitle,
            'manga_slug' => $mangaSlug,
            'manga_url' => $mangaUrl,
            'manga_source_slug' => "{$this->mangaSource}-{$mangaSlug}"
        ]);

        $chapters = $this->manager->processMangaChapter($source, $manga);
        $chapters = isset($chapters['chapters']) && $chapters['chapters'] ? $chapters['chapters'] : [];

        return [
            'arguments' => Input::all(),
            'cacheAge' => $source['cacheAge'],
            'mangaTitle' => $mangaTitle,
            'mangaSlug' => $mangaSlug,
            'mangaUrl' => [$this->mangaSource => $mangaUrl],
            'chapters' => $chapters ? $chapters : false,
            'chaptersCount' => count($chapters)
        ];
    }

    /**
     * updateMangaChapter()
     *
     * @inheritDoc
     */
    public function updateMangaChapter($slug)
    {
        $manga = Manga::slug($slug)->first();

        if (!$manga) abort(404, 'Manga not found.');

        // kill process if source scrap url not found
        if (!isset($manga->manga_url) || !$manga->manga_url) abort(400, "Manga manga_url is not exist or empty.");

        $source = $this->manager->getSource($manga->manga_url, [], false);

        // kill process if source return false
        if (!$source['status']) abort(400, $source['error']);

        $chapters = $this->manager->processMangaChapter($source, $manga->toArray());
        $chapters = isset($chapters['chapters']) && $chapters['chapters'] ? $chapters['chapters'] : [];

        return [
            'arguments' => $slug,
            'cacheAge' => $source['cacheAge'],
            'manga' => $manga,
            'chapters' => $chapters,
            'chaptersCount' => count($chapters)
        ];
    }

    /**
     * updateMangaPage()
     *
     * @inheritDoc
     */
    public function updateMangaPage($manga_slug, $chapter_slug)
    {
        $chapter = MangaChapter::slugSource("{$this->mangaSource}-{$manga_slug}-{$chapter_slug}")->first();

        if (!$chapter) abort(404, 'Chapter not found.');

        // query and delete all relatad page with this chapter first
        $ids = MangaPage::where('manga_chapter_id', $chapter['manga_chapter_id'])->pluck('manga_page_id')->toArray();
        MangaPage::destroy($ids);

        if (!isset($chapter->manga_chapter_url) || !$chapter->manga_chapter_url) abort(400, "Chapter manga_chapter_url is not exist or empty.");

        $cleanPageUrl = str_replace('1.html', '', $chapter->manga_chapter_url);
        $source = $this->manager->getSource($chapter->manga_chapter_url, [], false);

        // kill process if source return false
        if (!$source['status']) abort(400, $source['error']);

        $manga = $chapter->manga;

        $doc = new Document($source['content']);

        // kill process if cannot found element to make total manga chapter pages
        if (!$doc->has('#top_bar') || !isset($doc->find('#top_bar')[0])) abort(400, 'Total pages element not found.');

        $total = $doc->find('#top_bar');
        $total = $total[0]->find('select.m option');
        $total = count($total) - 1;

        for ($page_id = 1; $page_id <= $total; $page_id++) {

            $data = [
                'manga_page_order' => $page_id,
                'manga_page_slug' => "{$chapter->manga_chapter_slug}-{$page_id}",
                'manga_page_source_slug' => "{$this->mangaSource}-{$chapter->manga_chapter_slug}-{$page_id}",
                'manga_page_img_src' => null,
                'manga_page_url' => null,
                'manga_id' => $manga->manga_id,
                'manga_chapter_id' => $chapter->manga_chapter_id,
                'source_id' => $manga->source_id
            ];

            $data['manga_page_url'] = $cleanPageUrl . $page_id . '.html';
            $source = $this->manager->getSource($data['manga_page_url'], [], false);

            if ($source['status'] && $source['content']) {
                $pageParse = new Document($source['content']);
                $data['manga_page_img_src'] = $pageParse->has('img#image') ? $pageParse->find('img#image')[0]->getAttribute('src') : null;
            }

            $page = $this->manager->saveMangaPage($data);

            $pages[] = $page;
        }

        return [
            'arguments' => [
                'manga_slug' => $manga_slug,
                'manga_chapter' => $chapter_slug
            ],
            'cacheAge' => $source['cacheAge'],
            'manga' => $manga,
            'chapter' => $chapter,
            'pages' => $pages
        ];
    }

    /**
     * saveMangaPageImages()
     *
     * @inheritDoc
     */
    public function saveMangaPageImages($manga_slug, $chapter_slug)
    {
        $images = [];
        $chapter = MangaChapter::slug("{$manga_slug}-{$chapter_slug}")->first();

        if (!$chapter) abort(404, 'Chapter not found.');

        foreach ($chapter->pages->toArray() as $page) {

            $img = $this->manager->getSource($page['manga_page_img_src']);

            if (!$img['status'] || !$img['content']) abort(400, __METHOD__ . ' fail to fetch image.');

            $page['image'] = $this->saveImage([
                'manga_slug' => $chapter->manga->manga_slug,
                'manga_chapter_slug' => $chapter->manga_chapter_slug,
                'manga_page_slug' => $page['manga_page_slug'],
                'manga_page_img_src' => $page['manga_page_img_src'],
                'content' => $img['content']
            ]);

            $images[] = $page;
        }

        return $images;
    }

    /**
     * saveImage()
     *
     * @inheritDoc
     */
    public function saveImage($options) {

        if (!isset($options['manga_slug']) || !$options['manga_slug']) abort(400, __METHOD__ . ' manga_slug is not exist or empty.');
        if (!isset($options['manga_chapter_slug']) || !$options['manga_chapter_slug']) abort(400, __METHOD__ . ' manga_chapter_slug is not exist or empty.');
        if (!isset($options['manga_page_slug']) || !$options['manga_page_slug']) abort(400, __METHOD__ . ' manga_page_slug is not exist or empty.');
        if (!isset($options['manga_page_img_src']) || !$options['manga_page_img_src']) abort(400, __METHOD__ . ' manga_page_img_src is not exist or empty.');
        if (!isset($options['content']) || !$options['content']) abort(400, __METHOD__ . ' content is not exist or empty.');

        $ext = pathinfo($options['manga_page_img_src'], PATHINFO_EXTENSION);

        // init save directory
        $folder = public_path("ragnarokz-content/manga/{$options['manga_slug']}/{$options['manga_chapter_slug']}");

        if (!is_dir($folder)) mkdir($folder, 0755, true);

        $options['save_path'] = "{$folder}/{$options['manga_page_slug']}.{$ext}";
        $options['save_status'] = file_put_contents($options['save_path'], $options['content']);
        $options['save_status'] = $options['save_status'] ? true : false;
        unset($options['content']);

        return $options;
    }
}
