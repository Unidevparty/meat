<?php

namespace App\Http\Controllers\Admin;

use View;
use App\ForumOnMain;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ForumOnMainController extends Controller
{
    public function __construct()
    {
        checkPermissions('articles');

        View::share('section_name', 'Форум в статьях на главной');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        View::share('page_name', 'Список');

        $forums = ForumOnMain::latest()
            ->paginate();

        return view('admin.forum_on_main.list', compact('forums'));
    }

    public function create()
    {
        View::share('page_name', 'Создание записи');

        return view('admin.forum_on_main.form');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'forum_id' => 'required',
            'source_image' => 'required',
            'image' => 'required',
            'position' =>  'required|integer|min:1',
        ]);

        $data = $request->all();

        if (!$data['name']) {
            $data['name'] = $data['forum_id'];
        }

        $forum = ForumOnMain::create($data);

        flash('Запись успешно создана', 'success');

        return redirect(route('forum_on_main.edit', $forum->id));
    }

    
    public function edit($id)
    {
        $forum = ForumOnMain::findOrFail($id);

        return view('admin.forum_on_main.form', compact('forum'));
    }

    public function update(Request $request, $id)
    {
        $forum = ForumOnMain::findOrFail($id);

        $this->validate($request, [
            'name' => 'required',
            'forum_id' => 'required',
            'position' =>  'required|integer|min:1',
        ]);

        $data = $request->all();

        if (!$data['name']) {
            $data['name'] = $data['forum_id'];
        }

        $forum->update($data);

        flash('Запсиь успешно обновлена', 'success');

        return redirect(route('forum_on_main.edit', $forum->id));
    }

    public function destroy($id)
    {
        $forum = ForumOnMain::findOrFail($id);

        // Удаляем картинки
        $images = [
            $forum->source_image,
            $forum->image,
            $forum->big_on_main_slider,
            $forum->sm_on_main_slider
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        $forum->delete();

        flash('Запись успешно удалена', 'success');

        return redirect(route('forum_on_main.index'));
    }
}
