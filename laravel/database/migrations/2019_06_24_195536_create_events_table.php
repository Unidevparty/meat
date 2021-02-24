<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->increments('id');

            $table->string('name', 191);
			$table->string('alias', 191);
			$table->string('title', 191)->nullable();
			$table->text('description', 65535)->nullable();
			$table->string('keywords', 191)->nullable();
			$table->string('source_image', 191)->nullable();
			$table->string('main_image', 191)->nullable();
			$table->string('preview', 191)->nullable();
			$table->text('introtext', 65535)->nullable();
			$table->text('text')->nullable();
			$table->integer('views')->nullable();
			$table->dateTime('published_at')->nullable();
			$table->boolean('published')->default(0);

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
        Schema::dropIfExists('events');
    }
}
