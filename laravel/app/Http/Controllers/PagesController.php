<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Page;
use View;

class PagesController extends Controller
{
    public function about()
    {
    	return view('pages.about');
    }
    public function advertising()
    {
    	return view('pages.advertising');
    }
    public function departments()
    {
    	return view('pages.departments');
    }
    public function verification()
    {
    	return view('pages.verification');
    }

    function showPage(Request $request) {
        $url = '/' . trim($request->path(), '/');

        $page = Page::where('url', $url)->published()->first();

        View::share('title', $page->title);
        View::share('keywords', $page->keywords);
        View::share('description', $page->description);
        

        return view('pages.' . $page->template, compact('page'));
    }
}
