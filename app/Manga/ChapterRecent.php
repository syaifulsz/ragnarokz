<?php

namespace App\Manga;

use Illuminate\Database\Eloquent\Model;
use \App\Components\MangaHelper;

class ChapterRecent extends Model
{
    protected $primaryKey = 'manga_chapter_recent_id';
    protected $table = 'manga_chapter_recents';
    protected $fillable = [
        'manga_id',
        'manga_chapter_id'
    ];

    public function chapter()
    {
        return $this->hasOne('App\Manga\Chapter', 'manga_chapter_id', 'manga_chapter_id');
    }

    public function manga()
    {
        return $this->hasOne('App\Manga\Manga', 'manga_id', 'manga_id');
    }

    public function _recent()
    {
        return [
            'order' => $this->chapter->_order(),
            'title' => $this->chapter->_titleMin(),
            'url' => $this->chapter->_url(),
            'time' => $this->created_at->diffForHumans()
        ];
    }

    public function _url()
    {
        return route('manga/chapter/page', [
            'manga_slug' => $this->manga->_slug(),
            'chapter' => $this->chapter->_slugOrder()
        ]);
    }

    public function _title()
    {
        return $this->chapter->_title();
    }
}
