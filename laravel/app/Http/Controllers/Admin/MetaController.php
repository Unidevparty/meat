<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Meta;
use View;

class MetaController extends Controller
{
    public function __construct()
    {
        checkPermissions('meta');

        View::share('section_name', 'Метатеги');
        View::share('page_name', '');
    }

    public function index()
    {
        $metas = Meta::all();
        return view('admin.meta', compact('metas'));
    }

    public function save(Request $request)
    {
        $data = $request->get('data');

        foreach ($data as $id => $values) {
            $meta = Meta::find($id);
            $meta->update($values);
            $meta->save();
        }

        $data = $request->get('new');

        foreach ($data as $values) {
            $meta = Meta::create($values);
            $meta->save();
        }

        flash('Мета успешно сохранены', 'success');

        return redirect()->back();
    }
}
