<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;

class PagesController extends Controller
{
	protected $views_path = '';

	public function __construct() {
		checkPermissions('templates');

		$this->views_path = app_path() . '/../resources/views/pages/';
		View::share('section_name', 'Страницы');
		View::share('page_name', '');
	}


    public function index()
    {
        View::share('page_name', 'Список');

		$files = scandir($this->views_path);

		$files = array_splice($files, 2);

		return view('admin.pages.list', compact('files'));
    }

    public function edit(Request $request, $file)
    {
		$path = $request->get('path');
        View::share('page_name', 'Редактирование');

		$file_content = file_get_contents($this->views_path . $path . $file);

		return view('admin.pages.form', compact('file', 'path', 'file_content'));
    }

    public function update(Request $request, $file)
    {
		$path = $request->get('path');
    	$file_content = $request->get('file_content');

        View::share('page_name', 'Редактирование');

		file_put_contents($this->views_path . $path . $file, $file_content);

        flash('Шаблон успешно обновлен', 'success');

		return view('admin.pages.form', compact('file', 'path', 'file_content'));
    }
}
