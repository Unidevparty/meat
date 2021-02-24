<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use View;
use App\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function __construct()
    {
        checkPermissions('banners');

        View::share('section_name', 'Баннеры');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        View::share('page_name', 'Список');

        $banners = null;
        $position = $request->get('position');

        if ($position) {
            $banners = Banner::where('position', $position);
        }

        $bydefault = $request->get('bydefault');
        if ($bydefault) {
            if ($banners) {

                $banners->where('bydefault', 1);
                
            } else {
                $banners = Banner::where('bydefault', 1);
            }
        }
        if ($banners) {
            $banners = $banners->paginate();
        }
        else {
            $banners = Banner::paginate();
        }

        $banners->appends(Input::all());

        return view('admin.banner.list', compact('banners', 'position', 'bydefault'));
    }

    public function create()
    {
        View::share('page_name', 'Создание баннера');

        $positions = Banner::POSITIONS;

        return view('admin.banner.form', compact('positions'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'main_image' => 'required',
            // 'mobile_image' => 'required',
            'url' => 'required',
        ]);

        $banner = Banner::create($request->all());

        flash('Баннер успешно создан', 'success');

        return redirect(route('banner.edit', $banner->id));
    }

    public function edit(Banner $banner)
    {
        View::share('page_name', 'Обновление баннера');

        $positions = Banner::POSITIONS;

        return view('admin.banner.form', compact('positions', 'banner'));
    }

    public function update(Request $request, Banner $banner)
    {
        $this->validate($request, [
            'name' => 'required',
            //'main_image' => 'required',
            // 'mobile_image' => 'required',
            'url' => 'required',
        ]);

        $banner->update($request->all());

        flash('Баннер успешно обновлен', 'success');

        return redirect(route('banner.edit', $banner->id));
    }

    public function destroy(Banner $banner)
    {
        // Удаляем картинки
        $images = [
            $banner->main_image,
            $banner->tablet_image,
            $banner->mobile_image
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        $banner->delete();

        flash('Баннер успешно удален', 'success');

        return redirect(route('banner.index'));
    }
}
