<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

use App\Http\Requests;

class AdminController extends Controller
{
    protected $layout = 'layouts.master';

    public function dashboard()
    {
        // view data
        $view = [
            'title' => 'Admin Dashboard'
        ];

        $this->breadcrumb['Dashboard'] = route('adminDashboard');
        View::share('breadcrumb', $this->breadcrumb);

        return view($this->layout, [
            'content' => view('admin.dashboard')->with($view)
        ]);
    }
}
