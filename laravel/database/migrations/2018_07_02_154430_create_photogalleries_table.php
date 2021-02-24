<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePhotogalleriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('photogalleries', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 191)->nullable();
            $table->string('alias', 191)->nullable();
            $table->string('title', 191)->nullable();
            $table->string('description', 191)->nullable();
            $table->string('keywords', 191)->nullable();
            $table->string('image', 191)->nullable();
            $table->mediumText('introtext')->nullable();
			$table->mediumText('text')->nullable();
            $table->mediumText('photos')->nullable();
            $table->datetime('published_at')->nullable();
            $table->boolean('published')->nullable();
            $table->integer('views')->nullable();
            $table->integer('company_id')->nullable();
            $table->integer('event_id')->nullable();
            $table->timestamps();
        });

        Schema::create('tags_photogalleries', function(Blueprint $table)
        {
            $table->increments('id');
            $table->string('name', 191);
            $table->string('alias', 191);
            $table->timestamps();
        });


        Schema::create('photogalleries_tags', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('photogallery_id');
			$table->integer('photogallery_tag_id');
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
        Schema::dropIfExists('photogalleries');
        Schema::dropIfExists('tags_photogalleries');
        Schema::dropIfExists('photogalleries_tags');
    }
}
