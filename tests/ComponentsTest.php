<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use \App\Components\MangaHelper;

class ComponentsTest extends TestCase
{
    /**
     * Manga title to slug conversion
     *
     * @return string
     */
    public function testSlugify()
    {
        $str = MangaHelper::slugify('This Is A Manga Title');
        $this->assertTrue(($str == 'this-is-a-manga-title'));
    }
}
