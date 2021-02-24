<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $companies = \App\Company::all();
        $backup = [];

        foreach ($companies as $company) {
            $backup[$company->id] = (int) $company->year;
            $company->year = null;
            $company->save();
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->datetime('year')->nullable()->change();
            $table->string('title', 191)->nullable();
            $table->string('alias', 191)->nullable();
			$table->text('description', 65535)->nullable();
            $table->string('keywords', 191)->nullable();
            $table->mediumText('introtext')->nullable();
			$table->mediumText('text')->nullable();
            $table->mediumText('contacts')->nullable();
            $table->string('email', 191)->nullable();
            $table->boolean('email_checked')->nullable();
            $table->integer('email_check_fails')->nullable();
            $table->mediumText('gallery')->nullable();
            $table->mediumText('videos')->nullable();
            $table->mediumText('files')->nullable();
            $table->datetime('published_at', 191)->nullable();
            $table->boolean('published', 191)->nullable();
            $table->unsignedInteger('views')->nullable();
            $table->unsignedInteger('holding_id')->nullable();
        });

        foreach ($backup as $id => $year) {
            $company = \App\Company::find($id);

            if ($company) {
                $company->year = \Carbon\Carbon::create($year, 1, 1, 0, 0, 0);
                $company->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $companies = \App\Company::all();
        $backup = [];

        foreach ($companies as $company) {
            $backup[$company->id] = $company->year;
            $company->year = null;
            $company->save();
        }

        Schema::table('companies', function (Blueprint $table) {
            $table->integer('year')->change();
            $table->dropColumn('title');
            $table->dropColumn('alias');
            $table->dropColumn('description');
            $table->dropColumn('keywords');
            $table->dropColumn('introtext');
            $table->dropColumn('contacts');
            $table->dropColumn('email');
            $table->dropColumn('email_checked');
            $table->dropColumn('email_check_fails');
            $table->dropColumn('text');
            $table->dropColumn('gallery');
            $table->dropColumn('videos');
            $table->dropColumn('files');
            $table->dropColumn('published_at');
            $table->dropColumn('published');
            $table->dropColumn('views');
            $table->dropColumn('holding_id');
        });

        foreach ($backup as $id => $year) {
            $company = \App\Company::find($id);

            if ($company) {
                $company->year = (int) $year;
                $company->save();
            }
        }
    }
}
