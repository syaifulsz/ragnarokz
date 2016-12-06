<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// Service
use \App\Providers\MangaServiceProvider;

// Mangas
use \App\Manga\Manga;

/**
 * TODO: MangaCommand Clean Up
 * TODO: Create an option and function to scrap chapters that has no pages
 */
class MangaCommand extends Command
{
    protected $signature = 'manga:scrap';
    protected $description = 'Command description';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $a = $this->chooseManga();
        $this->comment($a['manga']->_title());
        if ($a['option_id'] == 1) $this->scrapChapterByRange($a['manga'], $a['option_range']['start'], $a['option_range']['end']);
    }

    private $options = [
        1 => 'scrap by range',
        2 => 'scrap all'
    ];

    /**
     * Run command to give user option to choose manga and return with the
     * manga data
     * @return          {Array} array of seleted manga
     */
    public function chooseManga()
    {
        $optionRange = [
            'start' => null,
            'end' => null,
        ];

        $mangas = Manga::all();

        if (!$mangas->count()) abort(404, 'No manga found.');

        $headers = ['ID', 'Manga'];
        $rows = [];

        foreach ($mangas as $manga) {
            $rows[] = [$manga->manga_id, $manga->_title()];
            $manga_options[] = $manga->manga_id;
            // $manga_options[] = $manga->_title();
        }

        $this->table($headers, $rows);
        $manga_id = $this->anticipate('Choose your manga? ['. implode(', ', $manga_options) .']', $manga_options);
        if (!is_numeric($manga_id) || !in_array($manga_id, $manga_options)) abort(400, 'Invalid manga ID.');
        // $manga_id = $this->anticipate('Choose your manga?', $manga_options);
        // if (!in_array($manga_id, $manga_options)) abort(400, 'Invalid manga.');

        $options = ''; $i = 0;
        foreach ($this->options as $key => $except) {
            $i++; $options .= "{$key} => {$except}" . ($i != count($this->options) ? ', ' : '');
        }

        // $option_id = $this->ask('Choose your scrapping method? ['. $options .']');
        // if (!is_numeric($option_id) || !array_key_exists($option_id, $this->options)) abort(400, 'Invalid option ID.');

        // FIXME: Wrong manga by ID selection
        $option_id = 1;
        if ($option_id == 1) {
            $optionRange['start'] = $this->ask('Please add your "start" range? [0 - '. $manga->chapters->count() .']');
            if (!is_numeric($optionRange['start']) || ($optionRange['start'] > $manga->chapters->count())) abort(400, 'Invalid range start!.');

            $optionRange['end'] = $this->ask('Please add your "end" range? ['. $optionRange['start'] .' - '. $manga->chapters->count() .']');
            if (!is_numeric($optionRange['end']) || ($optionRange['end'] >= $optionRange['start'] && $optionRange['end'] > $manga->chapters->count())) abort(400, 'Invalid range end!.');
        }

        return [
            'manga' => $mangaM->where('manga_id', $manga_id) ? $mangaM->where('manga_id', $manga_id)->first() : null,
            'option_id' => $option_id,
            'option_range' => $optionRange
        ];
    }

    /**
     * Scrap manga chapter page from selected manga
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

    /**
     * Show all available mangas
     * @return          {Array} array of available mangas
     */
    public function mangas()
    {

    }

    /**
     * Register manga based on given manga url
     * @param           $manga_url manga url
     * @return          {Array} array of registered manga data
     */
    public function mangaAdd($manga_url)
    {

    }

    /**
     * Update manga chapters
     * @param           $manga_slug manga slug
     * @return          {Array} array of updated chapters
     */
    public function mangaUpdate($manga_slug)
    {

    }

    /**
     * Scrap page on each chapters that depends on scrapping options
     * @param           $manga_slug manga slug
     * @param           $options options for scrapping
     * @return          {Array} array of scrapped page chapters
     */
    public function mangaChapterSave($manga_slug, $options)
    {

    }
}
