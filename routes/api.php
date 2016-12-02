<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::group(['prefix' => 'manga/mangafox'], function() {

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangas
     * @api                {get} / all mangas
     * @apiDescription     returns json data for all available mangas
     */
    Route::get('/', ['uses' => 'MangafoxScrapperController@mangas']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaChapters
     * @api                {get} /:manga_slug show specific manga
     * @apiDescription     returns json data for all chapters of a specific manga
     */
    Route::get('{manga_slug}', ['uses' => 'MangafoxScrapperController@mangaChapters']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaDelete
     * @api                {post} /:manga_slug/ delete a manga
     * @apiDescription     delete manga and all chapter and page that belongs to it
     */
    Route::post('{manga_slug}/delete-manga', ['uses' => 'MangafoxScrapperController@mangaDelete']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaPages
     * @api                {get} /:manga_slug/:chapter_slug show pages
     * @apiDescription     show pages that belongs to a chapter and manga
     */
    Route::get('{manga_slug}/{chapter_slug}', ['uses' => 'MangafoxScrapperController@mangaPages']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaAdd
     * @api                {post} /add add manga
     * @apiParam           {String} manga_url example: http://mangafox.me/manga/onepunch_man/
     * @apiDescription     scrap manga and all its chapter and register them to the database
     */
    Route::post('add', ['uses' => 'MangafoxScrapperController@mangaAdd']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaChapterUpdate
     * @api                {post} /:manga_slug/update update chapter
     * @apiDescription     scrap and update latest chapters for specific manga
     */
    Route::post('{manga_slug}/update', ['uses' => 'MangafoxScrapperController@mangaChapterUpdate']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaPageUpdate
     * @api                {post} /:manga_slug/:chapter_slug/update update pages
     * @apiDescription     scrap, delete and update page for specific chapter and manga
     */
    Route::post('{manga_slug}/{chapter_slug}/update', ['uses' => 'MangafoxScrapperController@mangaPageUpdate']);

    /**
     * @apiGroup           manga/mangafox
     * @apiName            mangaPageSave
     * @api                {post} /:manga_slug/:chapter_slug/save save page images
     * @apiDescription     scrap, delete and update page for specific chapter and manga
     */
    Route::post('{manga_slug}/{chapter_slug}/save', ['uses' => 'MangafoxScrapperController@mangaPageSave']);

});
