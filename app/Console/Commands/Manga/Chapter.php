<?php

namespace App\Console\Commands\Manga;

use Illuminate\Console\Command;

class Chapter extends Command
{
    protected $signature = 'manga:chapter';
    protected $description = 'A console command to update manga chapters or scrap pages on each chapter.';

    private $options = [
        1 => 'Scrap chapter by range',
        2 => 'Scrap chapter with no pages',
        3 => 'Scrap chapter by range and with no pages'
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $a = $this->chooseManga();
        if ($a['option_id'] == 1) $this->scrapChapterByRange($a['manga'], $a['option_range']['start'], $a['option_range']['end']);
        if ($a['option_id'] == 2) $this->scrapChapterWithNoPages($a['manga']);
        if ($a['option_id'] == 3) $this->scrapChapterByRangeAndWithNoPages($a['manga'], $a['option_range']['start'], $a['option_range']['end']);
    }

    /**
     * Run command to give user option to choose manga and return with the
     * manga data
     *
     * @return          {Array} array of seleted manga
     */
    public function chooseManga()
    {
        $optionRange = [
            'start' => null,
            'end' => null,
        ];

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
        // $manga_id = $this->anticipate('Choose your manga?', $manga_options);
        // if (!in_array($manga_id, $manga_options)) abort(400, 'Invalid manga.');

        $options = '';
        $manga = $mangas->find($manga_id) ? $mangas->find($manga_id) : [];

        $scrappingOptionsTable = [];
        foreach ($this->options as $option => $method) {
            $scrappingOptionsTable[] = [$option, $method];
        }

        $this->table(['Option', 'Method'], $scrappingOptionsTable);
        $option_id = $this->ask('Choose your scrapping option?');
        if (!is_numeric($option_id) || !array_key_exists($option_id, $this->options)) abort(400, 'Invalid option ID.');

        $firstChapterOrder = $manga->chapters()->orderBy('manga_chapter_order')->first()->_order();
        $lastChapterOrder = $manga->chapters()->orderBy('manga_chapter_order', 'desc')->first()->_order();

        $results = [
            ['Manga', $manga->_title()],
            ['Scrapping Method', $this->options[$option_id]]
        ];

        if ($option_id == 1 || $option_id == 3) {
            $optionRange['start'] = $this->ask('Please add your "start" range? ['. $firstChapterOrder .' - '. $lastChapterOrder .']');
            if (!is_numeric($optionRange['start']) || ($optionRange['start'] > $lastChapterOrder)) abort(400, 'Invalid range start!.');

            $optionRange['end'] = $this->ask('Please add your "end" range? ['. $optionRange['start'] .' - '. $lastChapterOrder .']');
            if (!is_numeric($optionRange['end']) || ($optionRange['end'] >= $optionRange['start'] && $optionRange['end'] > $lastChapterOrder)) abort(400, 'Invalid range end!.');

            $results[] = ['Chapter Range', "{$optionRange['start']} - {$optionRange['end']}"];
        }

        $this->comment('You have chooose:');
        $this->table(['', ''], $results);

        return [
            'manga' => $manga,
            'option_id' => $option_id,
            'option_range' => $optionRange
        ];
    }

    /**
     * Scrap manga chapter page from selected manga
     *
     * @param           {Object}    $manga  manga object
     * @param           {Integer}   $from   range start from
     * @param           {Integer}   $to     range end to
     * @return          {Array}     array of finish scrapped chapters
     */
    public function scrapChapterByRange($manga, $from = 0, $to = 0)
    {
        $process_time_start = date('H:i:s', time());
        $process_microtime_start = microtime(true);

        $reportHeader = ['Chapter', 'Duration', 'Start at', 'End at', 'Status'];
        $report = [];
        $chapters = [];

        $this->info("\nUpdating chapters...");

        if ($from || $to) $this->line("Working on chapter from {$from} to {$to}...");

        $chapters = \App\Manga\Chapter::where('manga_id', $manga->manga_id)
            ->where('manga_chapter_order', '>=', $from)
            ->where('manga_chapter_order', '<=', $to)
            ->get();

        $bar = $this->output->createProgressBar($chapters->count());

        $service = new \App\Providers\MangaServiceProvider();

        foreach ($chapters as $chapter) {

            $error = '';
            $message = null;

            $time_start = date('H:i:s', time());
            $microtime_start = microtime(true);

            try {
                $service->updateMangaPage($manga->manga_slug, $chapter->_slugOrder());
            } catch(Throwable $t) {
                $error .= " Error updateMangaPage()";
            }

            try {
                $service->saveMangaPageImages($manga->manga_slug, $chapter->_slugOrder());
            } catch(Throwable $t) {
                $error .= " Error UpdateMangaPageImages()";
            }

            $time_end = date('H:i:s', time());
            $microtime_end = microtime(true);
            $microtime = $microtime_end - $microtime_start;
            $microtime = gmdate("H:i:s", $microtime);

            $message = $message ? $message : "Done";
            $message .= ", Time: {$microtime}, Start: {$time_start}, End: {$time_end}";
            $report[] = [$chapter->_title(), $microtime, $time_start, $time_end, ($error ? 'Failed' : 'Done')];

            $bar->advance();

            if (!$error) $this->line(" {$chapter->_title()} ({$message})");
            if ($error) $this->error(" {$chapter->_title()} ({$message})");
        }

        $process_time_end = date('H:i:s', time());
        $process_microtime_end = microtime(true);
        $process_microtime = $process_microtime_end - $process_microtime_start;
        $process_microtime = gmdate("H:i:s", $process_microtime);

        $this->line("\n");
        $reportTotal = count($report);
        $this->info("Chapter update completed! {$reportTotal} chapters updated! .\n");

        $this->table($reportHeader, $report);
        $this->line("\n");

        $this->comment("Time: {$process_microtime}, Start: {$process_time_start}, End: {$process_time_end}");
        $this->line("\n");
    }

    public function scrapChapterWithNoPages($manga)
    {
        $process_time_start = date('H:i:s', time());
        $process_microtime_start = microtime(true);

        $reportHeader = ['Chapter', 'Duration', 'Start at', 'End at', 'Status'];
        $report = [];
        $chapters = [];

        $this->info("\nUpdating chapters...");

        $chapters = $manga
            ->chapters()
            ->where(function($query) {
                $query->has('pages', '=', 0);
            })
            ->get();

        $bar = $this->output->createProgressBar($chapters->count());

        $service = new \App\Providers\MangaServiceProvider();

        foreach ($chapters as $chapter) {

            if (!$chapter->pages->count()) {
                $error = '';
                $message = null;

                $time_start = date('H:i:s', time());
                $microtime_start = microtime(true);

                try {
                    $service->updateMangaPage($manga->manga_slug, $chapter->_slugOrder());
                } catch(Throwable $t) {
                    $error .= " Error updateMangaPage()";
                }

                try {
                    $service->saveMangaPageImages($manga->manga_slug, $chapter->_slugOrder());
                } catch(Throwable $t) {
                    $error .= " Error UpdateMangaPageImages()";
                }

                $time_end = date('H:i:s', time());
                $microtime_end = microtime(true);
                $microtime = $microtime_end - $microtime_start;
                $microtime = gmdate("H:i:s", $microtime);

                $message = $message ? $message : "Done";
                $message .= ", Time: {$microtime}, Start: {$time_start}, End: {$time_end}";
                $report[] = [$chapter->_title(), $microtime, $time_start, $time_end, ($error ? 'Failed' : 'Done')];

                $bar->advance();

                if (!$error) $this->line(" {$chapter->_title()} ({$message})");
                if ($error) $this->error(" {$chapter->_title()} ({$message})");
            }
        }

        $process_time_end = date('H:i:s', time());
        $process_microtime_end = microtime(true);
        $process_microtime = $process_microtime_end - $process_microtime_start;
        $process_microtime = gmdate("H:i:s", $process_microtime);

        $this->line("\n");
        $reportTotal = count($report);
        $this->info("Chapter update completed! {$reportTotal} chapters updated! .\n");

        $this->table($reportHeader, $report);
        $this->line("\n");

        $this->comment("Time: {$process_microtime}, Start: {$process_time_start}, End: {$process_time_end}");
        $this->line("\n");
    }

    public function scrapChapterByRangeAndWithNoPages($manga, $from = 0, $to = 0)
    {
        $process_time_start = date('H:i:s', time());
        $process_microtime_start = microtime(true);

        $reportHeader = ['Chapter', 'Duration', 'Start at', 'End at', 'Status'];
        $report = [];
        $chapters = [];

        $this->info("\nUpdating chapters...");

        if ($from || $to) $this->line("Working on chapter from {$from} to {$to}...");

        $chapters = $manga
            ->chapters()
            ->where('manga_chapter_order', '>=', $from)
            ->where('manga_chapter_order', '<=', $to)
            ->where(function($query) {
                $query->has('pages', '=', 0);
            })
            ->get();

        $bar = $this->output->createProgressBar($chapters->count());

        $service = new \App\Providers\MangaServiceProvider();

        foreach ($chapters as $chapter) {

            $error = '';
            $message = null;

            $time_start = date('H:i:s', time());
            $microtime_start = microtime(true);

            try {
                $service->updateMangaPage($manga->manga_slug, $chapter->_slugOrder());
            } catch(Throwable $t) {
                $error .= " Error updateMangaPage()";
            }

            try {
                $service->saveMangaPageImages($manga->manga_slug, $chapter->_slugOrder());
            } catch(Throwable $t) {
                $error .= " Error UpdateMangaPageImages()";
            }

            $time_end = date('H:i:s', time());
            $microtime_end = microtime(true);
            $microtime = $microtime_end - $microtime_start;
            $microtime = gmdate("H:i:s", $microtime);

            $message = $message ? $message : "Done";
            $message .= ", Time: {$microtime}, Start: {$time_start}, End: {$time_end}";
            $report[] = [$chapter->_title(), $microtime, $time_start, $time_end, ($error ? 'Failed' : 'Done')];

            $bar->advance();

            if (!$error) $this->line(" {$chapter->_title()} ({$message})");
            if ($error) $this->error(" {$chapter->_title()} ({$message})");
        }

        $process_time_end = date('H:i:s', time());
        $process_microtime_end = microtime(true);
        $process_microtime = $process_microtime_end - $process_microtime_start;
        $process_microtime = gmdate("H:i:s", $process_microtime);

        $this->line("\n");
        $reportTotal = count($report);
        $this->info("Chapter update completed! {$reportTotal} chapters updated! .\n");

        $this->table($reportHeader, $report);
        $this->line("\n");

        $this->comment("Time: {$process_microtime}, Start: {$process_time_start}, End: {$process_time_end}");
        $this->line("\n");
    }
}
