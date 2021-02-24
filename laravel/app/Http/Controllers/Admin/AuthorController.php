<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Company;
use App\Author;
use View;

class AuthorController extends Controller
{
    public function __construct()
    {
        checkPermissions('authors');

        View::share('section_name', 'Авторы');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        View::share('page_name', 'Список');

        $search = $request->get('search');

        $authors = new Author;

        if ($search) {
            $authors = Author::where('name', 'like', '%' . $search . '%');
        }

        $authors = $authors->paginate();

        return view('admin.author.list', compact('authors', 'search'));
    }

    public function create()
    {
        View::share('page_name', 'Создание автора');

        $companies = get_companies_list();

        return view('admin.author.form', compact('companies'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'photo' => 'required'
        ]);

        $author = Author::create($request->all());

        flash('Автор успешно создан', 'success');

        return redirect(route('author.edit', $author->id));
    }

    public function edit(Author $author)
    {
        View::share('page_name', 'Обновление автора');

        $companies = get_companies_list();

        return view('admin.author.form', compact('author', 'companies'));
    }

    public function update(Request $request, Author $author)
    {
        $this->validate($request, [
            'name' => 'required',
            'photo' => 'required'
        ]);

        $author->update($request->all());

        flash('Автор успешно обновлен', 'success');

        return redirect(route('author.edit', $author->id));
    }

    public function destroy(Author $author)
    {
        $author->delete();

        flash('Автор успешно удален', 'success');

        return redirect(route('author.index'));
    }

    public function search(Request $request) {
        $search = $request->get('query');

        $authors = new Author;

        if ($search) {
            $authors = Author::where('name', 'like', '%' . $search . '%');
        }

        $authors = $authors->limit(10)->get();

        $data = [
            'query' => 'Unit',
            'suggestions' => []
        ];


        if (!$authors) {
            return $data;
        }

        foreach ($authors as $author) {
            $data['suggestions'][] = [
                'value' => $author->name,
                'data' => route('author.edit', $author->id),
            ];
        }

        return $data;
    }
}
