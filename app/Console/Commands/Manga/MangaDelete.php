<?php

namespace App\Console\Commands\Manga;

use Illuminate\Console\Command;

class MangaDelete extends Command
{
    protected $signature = 'manga:delete';
    protected $description = 'Delete manga';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $mangas = \App\Manga\Manga::all();

        if (!$mangas->count()) abort(404, 'No manga found.');

        $headers = ['ID', 'Manga'];
        $rows = [];

        foreach ($mangas as $manga) {
            $rows[] = [$manga->manga_id, $manga->_title()];
            $manga_options[] = $manga->manga_id;
        }

        $this->table($headers, $rows);
        $manga_id = $this->anticipate('Choose your manga? ['. implode(', ', $manga_options) .']', $manga_options);
        if (!is_numeric($manga_id) || !in_array($manga_id, $manga_options)) abort(400, 'Invalid manga ID.');

        $options = '';
        $manga = $mangas->find($manga_id) ? $mangas->find($manga_id) : [];

        $confirm = $this->ask('Are you sure you want to delete "'. $manga->_title() .'"? [yes|no]');

        if ($confirm == 'yes') {
            $service = new \App\Providers\MangaServiceProvider();
            $service->deleteManga($manga->_slug());
        }
    }
}
