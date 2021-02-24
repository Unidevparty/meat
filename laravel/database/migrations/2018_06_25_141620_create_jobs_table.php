<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateJobsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('jobs', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name', 191);
			$table->string('alias', 191);
			$table->dateTime('published_at')->nullable();
			$table->boolean('published')->nullable();
			$table->boolean('fixed')->nullable();
			$table->dateTime('fixed_to')->nullable();
			$table->boolean('our')->nullable();
			$table->boolean('closed')->nullable();
			$table->integer('close_id')->nullable();
			$table->string('title', 191)->nullable();
			$table->string('keywords', 191)->nullable();
			$table->string('description', 191)->nullable();
			$table->integer('company_type_id');
			$table->integer('company_id');
			$table->text('introtext', 65535)->nullable();
			$table->string('city', 191)->nullable();
			$table->string('address', 191)->nullable();
			$table->float('zarplata', 10, 0)->nullable();
			$table->text('zp_options', 65535)->nullable();
			$table->text('signature', 65535)->nullable();
			$table->text('visibility', 65535)->nullable();
			$table->text('obyazannosti')->nullable();
			$table->text('trebovaniya')->nullable();
			$table->text('usloviya')->nullable();
			$table->integer('views')->nullable();
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
		Schema::drop('jobs');
	}

}
