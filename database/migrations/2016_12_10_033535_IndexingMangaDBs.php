<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IndexingMangaDBs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('manga_pages', function (Blueprint $table) {
            $table->index('manga_id');
            $table->index('manga_chapter_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('manga_pages', function (Blueprint $table) {
            $table->dropIndex('manga_id');
            $table->dropIndex('manga_chapter_id');
        });
    }
}
