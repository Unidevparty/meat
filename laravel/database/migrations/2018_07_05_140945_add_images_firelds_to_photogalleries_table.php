<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImagesFireldsToPhotogalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('photogalleries', function (Blueprint $table) {
            $table->string('main_image', 191)->nullable();
            $table->string('category_image', 191)->nullable();
            $table->string('home_image_1', 191)->nullable();
            $table->string('home_image_2', 191)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('photogalleries', function (Blueprint $table) {
            $table->dropColumn('main_image');
            $table->dropColumn('category_image');
            $table->dropColumn('home_image_1');
            $table->dropColumn('home_image_2');
        });
    }
}
