<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MangaChapter extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manga_chapters', function (Blueprint $table) {
            $table->increments('manga_chapter_id');
            $table->float('manga_chapter_order');
            $table->char('manga_chapter_title')->nullable();
            $table->char('manga_chapter_slug');
            $table->char('manga_chapter_source_slug')->unique();
            $table->char('manga_chapter_url')->nullable();
            $table->char('manga_id');
            $table->char('source_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('manga_chapters');
    }
}
