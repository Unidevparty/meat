<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBannersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('banners', function(Blueprint $table)
		{
			$table->increments('id');
			$table->text('name', 65535);
			$table->dateTime('start_date')->nullable();
			$table->dateTime('end_date')->nullable();
			$table->text('number', 65535)->nullable();
			$table->text('url', 65535);
			$table->text('main_image', 65535);
			$table->text('tablet_image', 65535);
			$table->text('mobile_image', 65535);
			$table->text('position', 65535);
			$table->integer('views')->nullable();
			$table->integer('clicks')->nullable();
			$table->boolean('published');
			$table->boolean('bydefault');
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
		Schema::drop('banners');
	}

}
