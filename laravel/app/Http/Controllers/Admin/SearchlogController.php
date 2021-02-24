<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SearchLog;
use Excel;
use View;
use Carbon\Carbon;

class SearchlogController extends Controller
{
    public function __construct()
    {
        checkPermissions('search');

        View::share('section_name', 'Логи поиска');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        View::share('page_name', 'Список');

        $searchlog = SearchLog::orderBy('id', 'desc');

        if ($start = $request->get('start')) {
            $searchlog->where('added_at', '>=', Carbon::parse($start));
        }

        if ($end = $request->get('end')) {
            $searchlog->where('added_at', '<=', Carbon::parse($end)->setTime(23, 59, 59));
        }


        $searchlog = $searchlog->paginate();

        return view('admin.searchlog.list', compact('searchlog'));
    }

    public function download(Request $request)
    {

        $searchlog = SearchLog::orderBy('id', 'asc');

        $start_time = '';
        $end_time   = Carbon::now();

        if ($start = $request->get('start')) {
            $start_time = Carbon::parse($start)->setTime(0, 0, 0);
            $searchlog->where('added_at', '>=', $start_time);
        }

        if ($end = $request->get('end')) {
            $end_time = Carbon::parse($end)->setTime(23, 59, 59);
            $searchlog->where('added_at', '<=', $end_time);
        }

        $searchlog = $searchlog->get()->toArray();
		array_unshift($searchlog, ['ID', 'IP', 'Пользователь', 'Дата', 'Запрос']);

        $filename = 'Лог поиска';
        if ($start_time) {
            $filename .= ' за ' . $start_time->format('Y-m-d') . ' - ' . $end_time->format('Y-m-d');
        } else {
            $filename .= ' до ' . $end_time->format('Y-m-d');
        }

        Excel::create($filename, function($excel) use ($searchlog) {
            $excel->sheet('Лист1', function($sheet) use ($searchlog) {
                $sheet->fromArray($searchlog);
            });
        })->export('xls');
    }
}
