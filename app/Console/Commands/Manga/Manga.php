<?php

namespace App\Console\Commands\Manga;

use Illuminate\Console\Command;

class Manga extends Command
{
    protected $signature = 'manga:add {url}';
    protected $description = 'Add new manga';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = $this->argument('url');
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) abort(400, 'Invalid manga url!');

        $service = new \App\Providers\MangaServiceProvider();

        $this->info("Adding new manga...");

        $manga = $service->addManga($url);
        $bar = $this->output->createProgressBar(count($manga['chapters']));

        foreach ($manga['chapters'] as $chapter) {
            $chapters[$chapter['manga_chapter_order']] = $chapter['manga_chapter_title'];
        }

        sort($chapters);

        foreach ($chapters as $chapter_key => $chapter_title) {
            $bar->advance();
            $this->line(" {$chapter_key}: {$chapter_title}");
        }

        $this->line("\n");
        $this->info("Successfully added new manga. \n");
        $this->line("Title: {$manga['mangaTitle']}");
        $this->line("Slug: {$manga['mangaSlug']}");
        $this->line("There are {$manga['chaptersCount']} chapters found and added to this manga.");
    }
}
