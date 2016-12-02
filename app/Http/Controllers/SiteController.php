<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class SiteController extends Controller
{
    public function formsReport()
    {
        // set breadcrumb
        $this->breadcrumb["Report"] = route('forms/report');
        $data['breadcrumb'] = $this->breadcrumb;

        View::share($data);
        return view('forms.report');
    }
}
