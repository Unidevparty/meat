<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateArticlesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('articles', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->string('alias', 191);
			$table->string('title', 191)->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('keywords', 191)->nullable();
			$table->string('source_image', 191)->nullable();
			$table->string('main_image', 191)->nullable();
			$table->string('preview', 191)->nullable();
			$table->string('preview_big')->nullable();
			$table->string('on_main', 191)->nullable();
			$table->string('big_on_main_slider')->nullable();
			$table->string('sm_on_main_slider')->nullable();
			$table->text('introtext', 65535)->nullable();
			$table->text('text')->nullable();
			$table->integer('views')->nullable();
			$table->string('author', 191)->nullable();
			$table->string('author_img', 191)->nullable();
			$table->integer('company')->nullable();
			$table->string('textru_uid', 192)->nullable()->default('');
			$table->dateTime('published_at')->nullable();
			$table->boolean('published');
			$table->boolean('for_registered');
			$table->timestamps();
			$table->text('textru');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('articles');
	}

}
