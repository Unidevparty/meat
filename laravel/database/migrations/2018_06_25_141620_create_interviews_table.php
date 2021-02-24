<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateInterviewsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('interviews', function(Blueprint $table)
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
			$table->string('fio', 191)->nullable();
			$table->string('post', 191)->nullable();
			$table->integer('company_id')->nullable();
			$table->text('introtext', 65535)->nullable();
			$table->text('text')->nullable();
			$table->text('quote', 65535)->nullable();
			$table->integer('views')->nullable();
			$table->text('textru_uid', 65535)->nullable();
			$table->dateTime('published_at')->nullable();
			$table->boolean('published');
			$table->text('author', 65535)->nullable();
			$table->text('author_img', 65535)->nullable();
			$table->boolean('for_registered')->nullable();
			$table->timestamps();
			$table->text('textru')->nullable();
			$table->boolean('main_slider')->nullable();
			$table->integer('main_slider_position')->nullable();
			$table->string('main_slider_source_img', 191)->nullable();
			$table->string('main_slider_big_img', 191)->nullable();
			$table->string('main_slider_sm_img', 191)->nullable();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('interviews');
	}

}
