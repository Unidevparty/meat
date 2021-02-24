<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use View;
use Illuminate\Support\Facades\Auth;
use App\Article;
use App\Interview;
use App\News;
use App\Textru;

class AdminController extends Controller
{
	
	public function __construct()
	{
		View::share('section_name', 'Панель управления');
		View::share('page_name', '');
	}

	public function index()
	{
		// $articles = Article::where('source_image', 'like', '/uploads/%')
		// 				   ->orWhere('main_image', 'like', '/uploads/%')
		// 				   ->orWhere('preview', 'like', '/uploads/%')
		// 				   ->orWhere('preview_big', 'like', '/uploads/%')
		// 				   ->orWhere('on_main', 'like', '/uploads/%')
		// 				   ->orWhere('big_on_main_slider', 'like', '/uploads/%')
		// 				   ->orWhere('sm_on_main_slider', 'like', '/uploads/%')
		// 				   ->get();
		// foreach ($articles as $article) {
		// 	$article->source_image = move_img($article->source_image, $article->name, $article->published_at);
		// 	$article->main_image = move_img($article->main_image, $article->name, $article->published_at);
		// 	$article->preview = move_img($article->preview, $article->name, $article->published_at);
		// 	$article->preview_big = move_img($article->preview_big, $article->name, $article->published_at);
		// 	$article->on_main = move_img($article->on_main, $article->name, $article->published_at);
		// 	$article->big_on_main_slider = move_img($article->big_on_main_slider, $article->name, $article->published_at);
		// 	$article->sm_on_main_slider = move_img($article->sm_on_main_slider, $article->name, $article->published_at);

		// 	$article->save();
		// }

		// $news = News::where('source_image', 'like', '/uploads/%')
		// 				   ->orWhere('main_image', 'like', '/uploads/%')
		// 				   ->orWhere('preview', 'like', '/uploads/%')
		// 				   ->orWhere('on_main', 'like', '/uploads/%')
		// 				   ->get();
		// foreach ($news as $new) {
		// 	$new->source_image = move_img($new->source_image, $new->name, $new->published_at);
		// 	$new->main_image = move_img($new->main_image, $new->name, $new->published_at);
		// 	$new->preview = move_img($new->preview, $new->name, $new->published_at);
		// 	$new->on_main = move_img($new->on_main, $new->name, $new->published_at);

		// 	$new->save();
		// }

		// $interviews = Interview::where('source_image', 'like', '/uploads/%')
		// 				   ->orWhere('main_image', 'like', '/uploads/%')
		// 				   ->orWhere('preview', 'like', '/uploads/%')
		// 				   ->get();
		// foreach ($interviews as $interview) {
		// 	$interview->source_image = move_img($interview->source_image, $interview->name, $interview->published_at);
		// 	$interview->main_image = move_img($interview->main_image, $interview->name, $interview->published_at);
		// 	$interview->preview = move_img($interview->preview, $interview->name, $interview->published_at);

		// 	$interview->save();
		// }

		// $banners = \App\Banner::all();
		// $abs_path = app_path() . "/../..";
		// foreach ($banners as $banner) {




		// 	if (strpos($banner->main_image, '/uploads/banners') !== 0) {
		// 		$new_path = str_replace('/uploads', '/uploads/banners', $banner->main_image);

		// 		var_dump(rename($abs_path . $banner->main_image, $abs_path . $new_path));

		// 		$banner->main_image = $new_path;
		// 	}

		// 	if (strpos($banner->tablet_image, '/uploads/banners') !== 0) {
		// 		$new_path = str_replace('/uploads', '/uploads/banners', $banner->tablet_image);

		// 		var_dump(rename($abs_path . $banner->tablet_image, $abs_path . $new_path));

		// 		$banner->tablet_image = $new_path;
		// 	}

		// 	if (strpos($banner->mobile_image, '/uploads/banners') !== 0) {
		// 		$new_path = str_replace('/uploads', '/uploads/banners', $banner->mobile_image);

		// 		var_dump(rename($abs_path . $banner->mobile_image, $abs_path . $new_path));

		// 		$banner->mobile_image = $new_path;
		// 	}

		// 	$banner->save();
		// }

		// dd(1);


		return view('admin.main');
	}


    public function profile()
    {
		View::share('page_name', 'Профиль');

		return view('admin.user.profile', compact('user', 'content'));
    }

    function textru() {
    	$uid = request()->get('uid');

		$data = [
			'text_unique' => json_decode(request()->get('text_unique'), 1),
			'json_result' => json_decode(request()->get('json_result'), 1),
			'spell_check' => json_decode(request()->get('spell_check'), 1),
		];

		$data2 = Textru::getResultPost($uid);

		if ($data2) {
			$data = $data2;
		}

		$news = News::where('textru_uid', $uid)->first();

		if ($news && $news->count()) {
			$news->textru = serialize($data);
			$news->save();
			return 'ok';
		}

		$articles = Article::where('textru_uid', $uid)->first();

		if ($articles && $articles->count()) {
			$articles->textru = serialize($data);
			$articles->save();
			return 'ok';
		}

		$interviews = Interview::where('textru_uid', $uid)->first();

		if ($interviews && $interviews->count()) {
			$interviews->textru = serialize($data);
			$interviews->save();
			return 'ok';
		}

		return 'ok';
    }


    function textru_recheck($uid) {

		$data = Textru::getResultPost($uid);

		if ($data) {
			flash('Данные обновлены', 'success');

			$news = News::where('textru_uid', $uid)->first();

			if ($news && $news->count()) {
				$news->textru = serialize($data);
				$news->save();
				return redirect()->back();
			}

			$articles = Article::where('textru_uid', $uid)->first();

			if ($articles && $articles->count()) {
				$articles->textru = serialize($data);
				$articles->save();
				return redirect()->back();
			}

			$interviews = Interview::where('textru_uid', $uid)->first();

			if ($interviews && $interviews->count()) {
				$interviews->textru = serialize($data);
				$interviews->save();
				return redirect()->back();
			}
		} else {
			flash('Данные еще не готовы', 'warning');
		}

		return redirect()->back();
    }
}

// function move_img($src, $name, $date) {
// 	if (strpos($src, '/uploads/') !== 0) {
// 		return $src;
// 	}

// 	$src = app_path() . "/../.." . $src;
// 	// Папка с картинками
//  	$img_path = config('app.content_images_path');

// 	$date = $date->format('Y/m/');
// 	$folder = app_path() . "/../.." . $img_path . $date;

// 	if (!is_dir($folder)) {
// 		mkdir($folder, 0777, 1);
// 	}

// 	$ext = '.' . end(($path = explode('.', $src)));

// 	$name = build_file_name($name);
// 	$tmp_name = $name;
// 	$i = 0;

// 	while(is_file($folder . $tmp_name . $ext)) {
// 		$tmp_name = $name . $i;
// 	}

// 	$image = $folder . $tmp_name . $ext;

//     var_dump(rename($src, $image));

// 	return $img_path . $date . $tmp_name . $ext;
// }
