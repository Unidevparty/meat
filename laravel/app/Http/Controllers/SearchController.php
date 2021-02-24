<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Search;
use App\SearchLog;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('search');

        if ($query) {
            SearchLog::create([
                'ip' => $request->ip(),
                'username' => member()->name,
                'query' => $query
            ]);
        }

        $where = $request->get('where', []);
       // DB::enableQueryLog();

        $replaced_query = Search::replace_search($query);

        $results = Search::search($replaced_query)->get();

        $results2 = Search::customSearch($query)->get();

        $results = $results2->merge($results);




        //dd($replaced_query, DB::getQueryLog(), count($results), count($results2), count($results3));


        if (count($where) && !in_array('everywhere', $where)) {
            $where = array_map(function($element) {
                $element = ucfirst($element);
                return "App\\$element";
            }, $where);

            $results = $results->whereIn('searchable_type', $where);
        }

        $order_by = $request->get('order_by');
        $order_dir = $request->get('order_dir');

        $order_select_value = $order_by . '-' . $order_dir;

        if (($order_by == 'views' || $order_by = 'published_at') && $order_dir == 'asc') {
            $results = $results->sortBy($order_by);
        }

        if (($order_by == 'views' || $order_by = 'published_at') && $order_dir == 'desc') {
            $results = $results->sortByDesc($order_by);
        }

        $results = $results->paginate(10)->appends(['search' => $query]);


        if ($search_request = $request->get('search')) {
            $total = $results->total();
            $title = 'Результаты поиска (' . $total . ') по запросу: "' . $search_request . '" - Мясной Эксперт';
            if (!$total) {
                $title = 'По запросу "' . $request->get('search') . '" ничего не найдено - Мясной Эксперт';
            }

            \View::share(['title' => $title]);
        }

        if($request->ajax()){
            return view('search.results_page', compact('results'));
        }

        return view('search.results', compact('results', 'order_select_value'));
    }
}
