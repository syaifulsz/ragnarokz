<?php

namespace App\Manga;

use Illuminate\Database\Eloquent\Model;

class Manga extends Model
{
    protected $primaryKey = 'manga_id';
    protected $table = 'mangas';
    protected $fillable = [
        'manga_title',
        'manga_slug',
        'manga_source_slug',
        'manga_url',
        'source_id'
    ];

    public $mangaSource = 'mangafox';

    public function source()
    {
        return $this->hasOne('App\Manga\Source', 'source_id', 'source_id');
    }

    public function chapters()
    {
        return $this->hasMany('App\Manga\Chapter', 'manga_id', 'manga_id');
    }

    public function recents()
    {
        return $this->hasMany('App\Manga\ChapterRecent', 'manga_id', 'manga_id');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('manga_slug', $slug);
    }

    public function scopeSlugSource($query, $slug)
    {
        return $query->where('manga_source_slug', $slug);
    }

    public function scopeChapter($query, $slug)
    {
        return $query->where('manga_chapter_slug', $slug);
    }

    public function _slug()
    {
        return $this->manga_slug;
    }

    public function _url()
    {
        return route('manga/chapter', ['manga_slug' => $this->manga_slug]);
    }

    public function _title()
    {
        return $this->manga_title;
    }

    /**
     * saveManga()
     *
     * @param  array $data
     * @return array
     */
    public function _save($data)
    {
        // abort if manga_source_slug not exist or empty
        if (!isset($data['manga_source_slug']) || !$data['manga_source_slug']) abort(400, __METHOD__ . ' manga_source_slug not exist or empty.');

        // abort if manga_slug not exist or empty
        if (!isset($data['manga_slug']) || !$data['manga_slug']) abort(400, __METHOD__ . ' manga_slug not exist or empty.');

        $error = null;

        // save manga source
        $sourceM = new \App\Manga\Source();
        $source = $sourceM->_save($this->mangaSource);

        if (!isset($source->source_id) || !$source->source_id) abort(400, __METHOD__ . ' source_id not exist or empty.');

        $data['source_id'] = $source->source_id;

        // check if manga already exist
        $manga = self::slugSource($data['manga_source_slug'])->first();
        if ($manga) {

            // if manga exist, only update manga
            try {
                self::slugSource($data['manga_source_slug'])->update([
                    'manga_title' => $data['manga_title'],
                    'manga_url' => $data['manga_url'],
                    'source_id' => $source->source_id
                ]);
            } catch(Throwable $t) {
                $error[] = $t;
            }
        } else {

            // if manga NOT exist, create new manga
            try {
                $manga = self::create($data);
            } catch(Throwable $t) {
                $error[] = $t;
            }
        }

        // abort if manga_id not exist
        if (!isset($manga->manga_id)) abort(400, __METHOD__ . ' manga_id not created.');

        $data['manga_id'] = $manga->manga_id;
        $data['error'] = $error;

        return $data;
    }

    /**
     * deleteManga()
     *
     * @param  string $slug
     * @return array
     */
    public function _deleteManga($slug)
    {
        $rmdir = false;
        $delete = false;
        $chapterM = new \App\Manga\Chapter();
        $relations = $chapterM->_deleteManga($slug);
        $manga = self::slug($slug)->first();
        if ($manga) {
            $rmdir = \App\Components\MangaHelper::deleteDir(public_path("ragnarokz-content/manga/$manga->manga_slug"));
            $delete = $manga->delete();
        }

        return [
            'manga' => $delete,
            'chapters_pages' => $relations,
            'rmdir' => $rmdir
        ];
    }
}
