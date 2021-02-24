<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateForumOnMainsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('forum_on_mains', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->string('forum_id', 191);
			$table->string('source_image', 191);
			$table->string('image', 191);
			$table->string('big_on_main_slider')->nullable();
			$table->string('sm_on_main_slider')->nullable();
			$table->integer('position');
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
		Schema::drop('forum_on_mains');
	}

}
