<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMangaPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manga_pages', function (Blueprint $table) {
            $table->increments('manga_page_id');
            $table->float('manga_page_order');
            $table->char('manga_page_slug');
            $table->char('manga_page_source_slug')->unique();
            $table->char('manga_page_url')->nullable();
            $table->char('manga_page_img_src')->nullable();
            $table->char('manga_id');
            $table->char('manga_chapter_id');
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
        Schema::dropIfExists('manga_pages');
    }
}
