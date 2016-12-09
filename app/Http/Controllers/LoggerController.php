<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Input;

// Vendors
use Carbon\Carbon;

// Mangas
use \App\Manga\Manga;
use \App\Manga\Chapter;
use \App\Manga\Page;
use \App\Manga\ChapterRecent;

class LoggerController extends \Rap2hpoutre\LaravelLogViewer\LogViewerController
{
    public function index()
    {
        $data = [
            'logs' => null,
            'files' => null,
            'current_file' => null,
            'breadcrumb' => []
        ];

        $parentController = parent::index();
        $controller = new \App\Http\Controllers\Controller();

        $controller->breadcrumb["Logger"] = route('logger');
        $data['breadcrumb'] = $controller->breadcrumb;

        $data['logs'] = $parentController->logs;
        $data['files'] = $parentController->files;
        $data['current_file'] = $parentController->current_file;

        View::share($data);
        return view('logger.logger');
    }
}
