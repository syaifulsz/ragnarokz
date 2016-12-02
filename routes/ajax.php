<?php

/*
|--------------------------------------------------------------------------
| Ajax Routes
|--------------------------------------------------------------------------
|
| Ajax route is used for ajax request made from front-end to backend.
| A small API to be used internally.
|
*/

Route::post('chapter-teaser', ['uses' => 'AjaxController@chapterTeaser']);
