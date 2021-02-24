<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;
use App\Page;

class PageController extends Controller
{
    protected $templates = [];

    public function __construct() {
        checkPermissions('pages');
        
        $views_path = app_path() . '/../resources/views/pages/';
        $files = scandir($views_path);
        $templates = array_splice($files, 2);
        $templates = array_map(function($f) {
            $f = explode('.', $f);
            return $f[0];
        }, $templates);

        $this->templates = array_combine($templates, $templates);

        View::share('section_name', 'Страницы');
        View::share('page_name', '');
    }

    
    public function index()
    {
        View::share('page_name', 'Список');

        $pages = Page::paginate();

        return view('admin.page.list', compact('pages'));
    }

    public function create()
    {
        View::share('page_name', 'Создание страницы');

        return view('admin.page.form', [
            'templates' => $this->templates
        ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'url' => 'required',
            'template' => 'required',
        ]);

        $page = Page::create($request->all());
        
        flash('Страница успешно создана', 'success');
        \Cache::forget('pages');

        return redirect(route('page.edit', $page->id));
    }

    public function edit(Page $page)
    {
        View::share('page_name', 'Обновление страницы');

        $templates = $this->templates;

        return view('admin.page.form', compact('templates', 'page'));
    }

    public function update(Request $request, Page $page)
    {
        $this->validate($request, [
            'name' => 'required',
            'url' => 'required',
            'template' => 'required',
        ]);

        $page->update($request->all());
        \Cache::forget('pages');

        flash('Страница успешно обновлена', 'success');

        return redirect(route('page.edit', $page->id));
    }

    public function destroy(Page $page)
    {
        $page->delete();
        \Cache::forget('pages');

        flash('Страница успешно удалена', 'success');

        return redirect(route('page.index'));
    }
}
