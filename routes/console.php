<?php

use Illuminate\Foundation\Inspiring;
use App\Http\Controllers\ScrapperController;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');

// php artisan manga:add {manga_url}
Artisan::command('manga:add {manga_url}', function ($manga_url) {

    if (filter_var($manga_url, FILTER_VALIDATE_URL) === FALSE) abort(400, 'Invalid manga url!');

    $service = new \App\Providers\MangaServiceProvider();

    $this->info("Adding new manga...");

    $manga = $service->addManga($manga_url);
    $bar = $this->output->createProgressBar(count($manga['chapters']));

    foreach ($manga['chapters'] as $chapter) {
        $chapters[$chapter['manga_chapter_order']] = $chapter['manga_chapter_title'];
    }

    sort($chapters);

    foreach ($chapters as $chapter_key => $chapter_title) {
        $bar->advance();
        $this->line(" {$chapter_key}: {$chapter_title}");
    }

    $bar->finish();
    $this->line("\n");
    $this->info("Successfully added new manga. \n");
    $this->line("Title: {$manga['mangaTitle']}");
    $this->line("Slug: {$manga['mangaSlug']}");
    $this->line("There are {$manga['chaptersCount']} chapters found and added to this manga.");
})->describe('Add new manga.');
