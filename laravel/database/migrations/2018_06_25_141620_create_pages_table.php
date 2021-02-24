<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('pages', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191)->nullable();
			$table->string('template', 191)->nullable();
			$table->string('url', 191)->nullable();
			$table->string('title', 191)->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('keywords', 191)->nullable();
			$table->text('text', 16777215)->nullable();
			$table->integer('views')->default(0);
			$table->dateTime('published_at')->nullable();
			$table->boolean('published')->nullable();
			$table->string('textru_uid', 191)->nullable();
			$table->text('textru', 65535)->nullable();
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
		Schema::drop('pages');
	}

}
