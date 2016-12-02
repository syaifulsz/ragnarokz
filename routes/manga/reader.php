<?php

// page image url
Route::get('/ragnarokz-content/manga/{manga_slug}/{manga_chapter_slug}/{manga_page_slug}.{ext}', ['as' => 'manga/page/image']);

// page cover thumb
Route::get('/ragnarokz-content/manga/{manga_slug}/{manga_chapter_slug}/cover/cover-{page_id}-thumb.jpg', ['as' => 'manga/chapter/cover-thumb']);

// manga url
Route::get('/', ['as' => 'home', 'uses' => 'ReaderController@index']);
Route::get('/{manga_slug}', ['as' => 'manga/chapter', 'uses' => 'ReaderController@showMangaChapters']);
Route::get('/{manga_slug}/{chapter}', ['as' => 'manga/chapter/page', 'uses' => 'ReaderController@showMangaPage']);
