<?php

namespace App\Providers\Managers;

// Vendors
use DiDom\Document;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

// Facedes
use Illuminate\Support\Facades\Cache;

// Models
use App\Manga\Manga;
use App\Manga\Chapter;
use App\Manga\Page;
use App\MangaPhoto;
use App\Manga\Source;

// Components
use MangaHelper;

class MangaManager implements MangaManagerInterface
{
    protected $mangaSource = 'mangafox';

    /**
     * processMangaChapter()
     *
     * @inheritDoc
     */
    public function processMangaChapter($source, $manga)
    {
        if (!isset($manga['manga_id']) || !$manga['manga_id']) abort(400, 'manga_id not exist or empty.');
        if (!isset($manga['manga_slug']) || !$manga['manga_slug']) abort(400, 'manga_slug not exist or empty.');
        if (!isset($manga['source_id']) || !$manga['source_id']) abort(400, 'source_id not exist or empty.');
        if (!isset($source['content']) || !$source['content']) abort(400, 'source_content not exist or empty.');

        $chapters = [];

        $doc = new Document($source['content']);
        $list = $doc->has('#chapters li') ? $doc->find('#chapters li') : [];

        // kill process if list is empty
        if (!$list) abort(400, 'No chapter element found.');

        foreach ($list as $chapterDom) {

            $chapterData = [
                'manga_chapter_title' => null,
                'manga_chapter_slug' => null,
                'manga_chapter_order' => null,
                'manga_chapter_url' => null,
                'manga_id' => $manga['manga_id'],
                'source_id' => $manga['source_id']
            ];

            $chapterDom__aTips = $chapterDom->has('a.tips') && isset($chapterDom->find('a.tips')[0]) ? $chapterDom->find('a.tips')[0] : false;

            if ($chapterDom__aTips) {
                $chapterData['manga_chapter_url'] = $chapterDom__aTips->getAttribute('href');
                $chapterData['manga_chapter_order'] = preg_replace("/[^0-9,.]/", '', $chapterDom__aTips->text());
            }

            $chapterData['manga_chapter_title'] = $chapterDom->has('span') && isset($chapterDom->find('span')[2]) ? $chapterDom->find('span')[2]->text() : null;
            $chapterData['manga_chapter_slug'] = MangaHelper::slugify($manga['manga_slug'] . '-' . $chapterData['manga_chapter_order']);
            $chapterData['manga_chapter_source_slug'] = "{$this->mangaSource}-{$chapterData['manga_chapter_slug']}";

            // saving mangaChapter
            $chapter = $this->saveMangaChapter($chapterData);

            $chapters[] = $chapter;
        }

        return [
            'document' => $doc,
            'chapters' => $chapters
        ];
    }

    /**
     * getSource()
     *
     * @inheritDoc
     */
    public function getSource($url, $args = [], $cache = true)
    {
        $source = [
            'cacheAge' => 'cold',
            'cacheName' => str_slug($url, '_'),
            'content' => null,
            'status' => false,
            'error' => null
        ];

        $source['content'] = Cache::get($source['cacheName']);

        if ($source['content']) $source['status'] = true;

        if (!$source['content'] || !$cache) {

            try {
                $client = new Client();
                $res = $client->get($url, $args);
                if ($res) {
                    $source['status'] = true;
                    $source['cacheAge'] = 'hot';
                    $source['content'] = $res->getBody()->getContents();
                    Cache::put($source['cacheName'], $source['content'], 60);
                }
            } catch (ClientException $e) {
                $source['status'] = false;
                $source['error'] = $e;
            } catch (Throwable $t) {
                $source['status'] = false;
                $source['error'] = $t;
            }
        }

        return $source;
    }

    /**
     * saveManga()
     *
     * @inheritDoc
     */
    public function saveManga($data)
    {
        $modal = new Manga();
        return $modal->_save($data);
    }

    /**
     * saveMangaChapter()
     *
     * @inheritDoc
     */
    public function saveMangaChapter($data)
    {
        $model = new Chapter();
        return $model->_save($data);
    }

    /**
     * saveMangaPage()
     *
     * @inheritDoc
     */
    public function saveMangaPage($data)
    {
        $model = new Page();
        return $model->_save($data);
    }

    /**
     * saveSource()
     *
     * @inheritDoc
     */
    public function saveSource($slug)
    {
        $model = new Source();
        return $model->_save($slug);
    }

    /**
     * deleteManga()
     *
     * @inheritDoc
     */
    public function deleteManga($slug)
    {
        $model = new Manga();
        return $model->_deleteManga($slug);
    }
}
