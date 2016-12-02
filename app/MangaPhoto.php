<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MangaPhoto extends Model
{
    protected $primaryKey = 'manga_photo_id';
    protected $table = 'manga_photos';
    protected $fillable = [
        'manga_id',
        'manga_chapter_id',
        'manga_page_id'
    ];

    public function manga()
    {
        return $this->hasOne('\App\Manga', 'manga_id', 'manga_id');
    }

    public function chapter()
    {
        return $this->hasOne('\App\MangaChapter', 'manga_chapter_id', 'manga_chapter_id');
    }

    public function page()
    {
        return $this->hasOne('\App\MangaPage', 'manga_page_id', 'manga_page_id');
    }
}
