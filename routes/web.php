<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('report', ['as' => 'forms/report', 'uses' => 'SiteController@formsReport']);
Route::get('logs', ['as' => 'logger', 'uses' => 'LoggerController@index'])->middleware('auth');

// Auth routes
include(base_path('routes/auth/auth.php'));

// After login routes
Route::group(['middleware' => 'auth'], function () {

    // Manga routes
    include(base_path('routes/manga/reader.php'));
});
