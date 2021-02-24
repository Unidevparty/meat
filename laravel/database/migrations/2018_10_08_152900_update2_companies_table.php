<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Update2CompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->boolean('is_holding')->nullable();
            $table->string('site')->nullable();
            $table->string('facebook')->nullable();
            $table->string('google_plus')->nullable();
            $table->string('vk')->nullable();
            $table->string('instagram')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
                $table->string('coords')->nullable();
                $table->string('country')->nullable();
                $table->string('region')->nullable();
                $table->string('city')->nullable();
            $table->mediumText('brands')->nullable();
            $table->float('rating')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('is_holding');
            $table->dropColumn('site');
            $table->dropColumn('facebook');
            $table->dropColumn('google_plus');
            $table->dropColumn('vk');
            $table->dropColumn('instagram');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('coords');
            $table->dropColumn('country');
            $table->dropColumn('region');
            $table->dropColumn('city');
            $table->dropColumn('brands');
            $table->dropColumn('rating');
        });
    }
}
