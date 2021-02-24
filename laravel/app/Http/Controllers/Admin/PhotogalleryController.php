<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use View;
use App\Photogallery;
use App\PhotogalleryTag;

class PhotogalleryController extends Controller
{
    public function __construct()
    {
        checkPermissions('photogallery');

        View::share('section_name', 'Фотогалерея');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        View::share('page_name', 'Список');

        $photogalleries = Photogallery::latest()->paginate();

		return view('admin.photogallery.list', compact('photogalleries'));
    }

    public function create()
    {
		View::share('page_name', 'Создание фотогалереи');

		$tags = PhotogalleryTag::pluck('name', 'name');
        $companies = get_companies_list();
        $events = get_events_list();

        return view('admin.photogallery.form', compact('tags', 'companies', 'events'));
    }

    public function store(Request $request)
    {
		$this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

        // if (!$data['title']) {
            $data['title'] = $data['name'] . ' - Мясной эксперт';
        // }

        if (!$data['description']) {
            $data['description'] = $data['introtext'];
        }

        $photogallery = Photogallery::create($data);
		$photogallery->updateTags($request->get('tags'));
        $photogallery->savePhotos($request);

        $photogallery->alias = $photogallery->id . '-' . $photogallery->alias;
        $photogallery->save();

        flash('Фотогалерея успешно создана', 'success');

        if ($request->ajax()) {
            return ['redirect' => route('photogallery.edit', $photogallery->id)];
        }

        return redirect(route('photogallery.edit', $photogallery->id));
    }

    public function edit(Photogallery $photogallery)
    {
		View::share('page_name', 'Редактирование новости');

		$tags = PhotogalleryTag::pluck('name', 'name');
        $companies = get_companies_list();
        $events = get_events_list();

        return view('admin.photogallery.form', compact('tags', 'photogallery', 'companies', 'events'));
    }

    public function update(Request $request, Photogallery $photogallery)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

        // if (!$data['title']) {
            $data['title'] = $data['name'] . ' - Мясной эксперт';
        // }

        if (!$data['description']) {
            $data['description'] = $data['introtext'];
        }

        $photogallery->update($data);
		$photogallery->updateTags($request->get('tags'));
        $photogallery->savePhotos($request);
        $photogallery->save();

        flash('Фотогалерея успешно обновлена', 'success');

        if ($request->ajax()) {
            return ['redirect' => route('photogallery.edit', $photogallery->id)];
        }

        return redirect(route('photogallery.edit', $photogallery->id));
    }

    public function destroy(Photogallery $photogallery)
    {
        // Удаляем картинки
        $images = [
            $photogallery->image,
            $photogallery->main_image,
            $photogallery->category_image,
            $photogallery->home_image_1,
            $photogallery->home_image_2
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        foreach ($photogallery->photos as $photo) {
            $photo = trim($photo->photo, '/');
            if (is_file($photo)) {
                unlink($photo);
            }
        }

		$photogallery->delete();

        flash('Фотогалерея успешно удалена', 'success');

        return redirect(route('photogallery.index'));
    }

    public function search(Request $request) {
        $query = trim($request->get('query'));
        $pages = Photogallery::limit(10)->where('name', 'LIKE', '%' . $query . '%')->get();

        $data = [
            'query' => 'Unit',
            'suggestions' => []
        ];


        if (!$pages) {
            return $data;
        }

        foreach ($pages as $page) {
            $data['suggestions'][] = [
                'value' => $page->name,
                'data' => route('photogallery.edit', $page->id),
            ];
        }

        return $data;
    }

}
