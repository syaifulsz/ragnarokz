<?php

namespace App\Manga;

use Illuminate\Database\Eloquent\Model;
use \App\Components\MangaHelper;
use \stojg\crop\CropEntropy;

class Chapter extends Model
{
    protected $primaryKey = 'manga_chapter_id';
    protected $table = 'manga_chapters';
    protected $fillable = [
        'manga_chapter_title',
        'manga_chapter_slug',
        'manga_chapter_source_slug',
        'manga_chapter_url',
        'manga_chapter_order',
        'manga_id',
        'source_id'
    ];

    public function recents()
    {
        return $this->hasMany('App\Manga\ChapterRecent', 'manga_chapter_id', 'manga_chapter_id');
    }

    public function source()
    {
        return $this->hasOne('App\Manga\Source', 'source_id', 'source_id');
    }

    public function manga()
    {
        return $this->hasOne('App\Manga\Manga', 'manga_id', 'manga_id');
    }

    public function pages()
    {
        return $this->hasMany('App\Manga\Page', 'manga_chapter_id', 'manga_chapter_id');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('manga_chapter_slug', $slug);
    }

    public function scopeSlugSource($query, $slug)
    {
        return $query->where('manga_chapter_source_slug', $slug);
    }

    public function _slug()
    {
        return $this->manga_chapter_slug;
    }

    public function _slugOrder()
    {
        return MangaHelper::slugify($this->manga_chapter_order);
    }

    public function _url()
    {
        return route('manga/chapter/page', [
            'manga_slug' => $this->manga->_slug(),
            'chapter' => $this->_slugOrder()
        ]);
    }

    public function _order()
    {
        return $this->manga_chapter_order;
    }

    public function _title()
    {
        return "{$this->manga_chapter_order}: {$this->manga_chapter_title}";
    }

    public function _titleMin()
    {
        return $this->manga_chapter_title;
    }

    public function _cover($meta = false)
    {
        $item = null;

        if ($imageCount = $this->pages->count()) {
            $middleId = ceil($imageCount/2);
            $item = $this->pages[$middleId]->_imageMeta();
            $imageCount = $this->pages->count();
            $items = [];
            foreach ($this->pages as $page) {
                if ($page->_order() >= 2 && $page->_order() < ($imageCount - 2)) {
                    $imageMeta = $page->_imageMeta();
                    $items[$imageMeta['width']] = $imageMeta;
                }
            }

            if (count($items) > 1) {
                krsort($items);
                $item = array_shift($items);
            }
        }

        return $meta ? $item : (!$item ? null : $item['src']);
    }

    public function _coverThumb($pageId = null)
    {
        $pageId = $pageId ? $pageId : $this->_cover(true)['order'];
        $coverThumb = route('manga/chapter/cover-thumb', ['manga_slug' => $this->manga->manga_slug, 'manga_chapter_slug' => $this->manga_chapter_slug, 'page_id' => $pageId]);
        $chapterPath = public_path("ragnarokz-content/manga/{$this->manga->_slug()}/{$this->_slug()}/cover/");
        $coverPath = $chapterPath . "cover-{$pageId}-thumb.jpg";
        $hasImage = true;

        if (!file_exists($coverPath)) {
            $hasImage = false;
            $page = $this->pages()->order($pageId)->first();

            if ($page) {
                $imgMeta = $page->_imageMeta();
                $imgPath = $imgMeta['path'];
                $center = new CropEntropy($imgPath);
                $croppedImage = $center->resizeAndCrop(500, 500);

                if (!is_dir($chapterPath)) mkdir($chapterPath, 0755, true);

                $croppedImage->writeimage($coverPath);

                $hasImage = true;
            }
        }

        return $hasImage ? $coverThumb : null;
    }

    /**
     * saveMangaChapter()
     *
     * @param  array $data
     * @return array
     */
    public function _save($data)
    {
        // abort if manga_chapter_source_slug not exist or empty
        if (!isset($data['manga_chapter_source_slug']) || !$data['manga_chapter_source_slug']) abort(400, __METHOD__ . ' manga_chapter_source_slug not exist or empty.');

        // abort if manga_chapter_slug not exist or empty
        if (!isset($data['manga_chapter_slug']) || !$data['manga_chapter_slug']) abort(400, __METHOD__ . ' manga_chapter_slug not exist or empty.');

        // abort if manga_id not exist or empty
        if (!isset($data['manga_id']) || !$data['manga_id']) abort(400, __METHOD__ . ' manga_id not exist or empty.');

        // abort if source_id not exist or empty
        if (!isset($data['source_id']) || !$data['source_id']) abort(400, __METHOD__ . ' source_id not exist or empty.');

        $error = null;

        $chapter = self::slugSource($data['manga_chapter_source_slug'])->first();
        if ($chapter) {
            try {
                self::slugSource($data['manga_chapter_source_slug'])->update([
                    'manga_chapter_title' => $data['manga_chapter_title'],
                    'manga_chapter_order' => $data['manga_chapter_order'],
                    'manga_chapter_url' => $data['manga_chapter_url'],
                    'manga_id' => $data['manga_id']
                ]);
            } catch(Throwable $t) {
                $error[] = $t;
            }
        } else {
            try {
                $chapter = self::create($data);
            } catch(Throwable $t) {
                $error[] = $t;
            }
        }

        // abort if manga_chapter_id not exist
        if (!isset($chapter->manga_chapter_id)) abort(400, __METHOD__ . ' manga_chapter_id not created.');

        $data['manga_chapter_id'] = $chapter->manga_chapter_id;
        $data['error'] = $error;

        return $data;
    }

    /**
     * deleteMangaChapters()
     *
     * @param  string $slug
     * @return array
     */
    public function _deleteManga($slug)
    {
        $chapter_ids = null;
        $page_ids = null;
        $manga = \App\Manga\Manga::slug($slug)->first();

        if ($manga) {
            $chapter_ids = self::where('manga_id', $manga->manga_id);
            if ($chapter_ids->count()) self::destroy($chapter_ids->pluck('manga_chapter_id')->toArray());

            $page_ids = self::where('manga_id', $manga->manga_id);
            if ($page_ids->count()) self::destroy($page_ids->pluck('manga_page_id')->toArray());
        }

        return [
            'chapter_count' => $chapter_ids,
            'page_count' => $page_ids
        ];
    }
}
