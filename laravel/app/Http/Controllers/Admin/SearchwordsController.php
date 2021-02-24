<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;
use App\Searchwords;

class SearchwordsController extends Controller
{
    public function __construct()
    {
        checkPermissions('search');

        View::share('section_name', 'Каталог коротких запросов');
        View::share('page_name', '');
    }

    public function index()
    {
        $searchwords = Searchwords::all();

        return view('admin.searchwords.form', compact('searchwords'));
    }

    public function save(Request $request)
    {
        $words = $request->get('words');

        $existed_words = [];

        foreach ($words as $word) {
            if (!$word['short']) continue;
            $searchword = Searchwords::firstOrNew(['short' => $word['short']]);
            $searchword->long = $word['long'];
            $searchword->save();

            $existed_words[] = $searchword->id;
        }

        Searchwords::whereNotIn('id', $existed_words)->delete();

        flash('Слова успешно сохранены', 'success');

        return redirect(route('searchwords.index'));
    }
}
