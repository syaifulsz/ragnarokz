<?php

namespace App\Manga;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $primaryKey = 'manga_page_id';
    protected $table = 'manga_pages';
    protected $fillable = [
        'manga_page_slug',
        'manga_page_source_slug',
        'manga_page_order',
        'manga_page_url',
        'manga_page_img_src',
        'manga_id',
        'manga_chapter_id',
        'source_id'
    ];

    public function source()
    {
        return $this->hasOne('App\Manga\Source', 'source_id', 'source_id');
    }

    public function manga()
    {
        return $this->hasOne('App\Manga\Manga', 'manga_id', 'manga_id');
    }

    public function chapter()
    {
        return $this->hasOne('App\Manga\Chapter', 'manga_chapter_id', 'manga_chapter_id');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('manga_page_slug', $slug);
    }

    public function scopeSlugSource($query, $slug)
    {
        return $query->where('manga_page_source_slug', $slug);
    }

    public function scopeOrder($query, $order)
    {
        return $query->where('manga_page_order', $order);
    }

    public function _order()
    {
        return $this->manga_page_order;
    }

    public function _slug()
    {
        return $this->manga_page_slug;
    }

    public function _imageMeta()
    {
        $ext = pathinfo($this->manga_page_img_src, PATHINFO_EXTENSION);
        $path = public_path("ragnarokz-content/manga/{$this->manga->manga_slug}/{$this->chapter->manga_chapter_slug}/{$this->manga_page_slug}.$ext");
        $getimagesize = getimagesize($path);
        return [
            'order' => $this->_order(),
            'src' => $this->_image(),
            'scrap' => $this->manga_page_img_src,
            'extension' => $ext,
            'path' => $path,
            'width' => $getimagesize[0],
            'height' => $getimagesize[1],
            'mime' => $getimagesize['mime'],
        ];
    }

    public function _image()
    {
        return route('manga/page/image', [
            'manga_slug' => $this->manga->_slug(),
            'manga_chapter_slug' => $this->chapter->_slug(),
            'manga_page_slug' => $this->_slug(),
            'ext' => pathinfo($this->manga_page_img_src, PATHINFO_EXTENSION),
        ]);
    }

    /**
     * saveMangaPage()
     *
     * @param  array $data
     * @return array
     */
    public function _save($data)
    {
        // abort if manga_page_source_slug not exist or empty
        if (!isset($data['manga_page_source_slug']) || !$data['manga_page_source_slug']) abort(400, __METHOD__ . ' manga_page_source_slug not exist or empty.');

        // abort if manga_chapter_slug not exist or empty
        if (!isset($data['manga_page_slug']) || !$data['manga_page_slug']) abort(400, __METHOD__ . ' manga_page_slug not exist or empty.');

        // abort if manga_chapter_id not exist or empty
        if (!isset($data['manga_chapter_id']) || !$data['manga_chapter_id']) abort(400, __METHOD__ . ' manga_chapter_id not exist or empty.');

        // abort if manga_id not exist or empty
        if (!isset($data['manga_id']) || !$data['manga_id']) abort(400, __METHOD__ . ' manga_id not exist or empty.');

        // abort if source_id not exist or empty
        if (!isset($data['source_id']) || !$data['source_id']) abort(400, __METHOD__ . ' source_id not exist or empty.');

        $error = null;

        $page = self::slug($data['manga_page_slug'])->first();
        if ($page) {
            try {
                self::where('manga_page_slug', $data['manga_page_slug'])->update([
                    'manga_page_slug' => $data['manga_page_slug'],
                    'manga_page_order' => $data['manga_page_order'],
                    'manga_page_img_src' => $data['manga_page_img_src'],
                    'manga_page_url' => $data['manga_page_url'],
                    'manga_id' => $data['manga_id'],
                    'manga_chapter_id' => $data['manga_chapter_id'],
                    'source_id' => $data['source_id'],
                ]);
            } catch(Throwable $t) {
                $error[] = $t;
            }
        } else {
            try {
                $page = self::create($data);
            } catch(Throwable $t) {
                $error[] = $t;
            }
        }

        $data['manga_page_id'] = $page->manga_page_id;
        $data['error'] = $error;

        return $data;
    }
}
