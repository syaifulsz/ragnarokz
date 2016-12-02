<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\Cache;
use DiDom\Document;
use DiDom\Query;

class HomeController extends Controller
{
    protected $layout = 'layouts.master';

    public function home()
    {
        $scrapper = new \App\Http\Controllers\ScrapperController();
        $mangas = $scrapper->getMangas();

        return view($this->layout, [
            'content' => view('home', ['mangas' => $mangas])
        ]);
    }
}
