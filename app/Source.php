<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    protected $primaryKey = 'source_id';
    protected $table = 'sources';
    protected $fillable = [
        'source_title',
        'source_slug'
    ];

    public function manga()
    {
        return $this->hasMany('App\Manga', 'source_id', 'source_id');
    }

    public function scopeSlug($query, $slug)
    {
        return $query->where('source_slug', $slug);
    }

    /**
     * _save()
     *
     * @param  string $sourceSlug
     * @return array
     */
    public function _save($slug)
    {
        $query = self::slug($slug)->first();
        if (!$query) {
            try {
                $query = self::create([
                    'source_slug' => $slug
                ]);
            } catch(Throwable $t) {
                abort(400, 'Unable to save new a manga source.', $t);
            }
        }
        return $query;
    }
}
