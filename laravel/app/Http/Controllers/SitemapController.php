<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use Carbon\Carbon;
use App\Article;
use App\Interview;
use App\Page;

class SitemapController extends Controller
{
    public function new_sitemap()
    {
        $news       = News::published()->get();
        $articles   = Article::published()->get();
        $interviews = Interview::published()->get();
        $pages      = Page::published()->get();

        return response(view('sitemap', compact(
	        	'news',
	        	'pages',
	        	'articles',
	        	'interviews'
        	)))
        	->header('Content-Type', 'text/xml');
    }


    public function index()
    {
        $content  = '<sitemapindex xmlns="https://www.sitemaps.org/schemas/sitemap/0.9">';
        $content .= '<sitemap> <loc>https://meat-expert.ru/forums/sitemap.php</loc> <lastmod>' . date('Y-m-d') . '</lastmod> </sitemap>';
        $content .= '<sitemap> <loc>https://meat-expert.ru/new_sitemap.xml</loc> <lastmod>' . date('Y-m-d') . '</lastmod> </sitemap>';
        $content .= '</sitemapindex>';

        return response($content)
            ->header('Content-Type', 'text/xml');
    }
}