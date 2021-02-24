<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\NewsTag;
use App\Company;
use App\Helpers\NotifyCompany;
use App\Textru;
use App\News;

use View;

class NewsController extends Controller
{

	public function __construct()
	{
        checkPermissions('news');

		View::share('section_name', 'Новости');
		View::share('page_name', '');
	}

    public function index(Request $request)
    {
        $start = $request->get('start', '');
        $end = $request->get('end', '');

        View::share('page_name', 'Список');

        $news = News::latest();

        if ($start) {
            $news->where('published_at', '>=', Carbon::parse($start));
        }

        if ($end) {
            $news->where('published_at', '<=', Carbon::parse($end));
        }

        $news = $news->paginate();

		return view('admin.news.list', compact('news', 'start', 'end'));
    }

    public function create()
    {
		View::share('page_name', 'Создание новости');

		$tags = NewsTag::pluck('name', 'name');
		$companies = Company::pluck('name', 'id')->toArray();
		$companies[0] = 'Не указано';
		ksort($companies);

        return view('admin.news.form', compact('tags', 'companies'));
    }

    public function store(Request $request)
    {
		$this->validate($request, [
            'name' => 'required',
            'source_image' => 'required'
        ]);

        $data = $request->all();

        $data['title'] = $data['name'] . ' - Мясной эксперт';

        $news = News::create($data);
		$news->updateTags($request->get('tags'));

        flash('Новость успешно создана', 'success');


        if ($request->get('check')) {
            $news->textru = '';
            $news->textru_uid = Textru::addPost($news->text);

            flash('Текст отправлен на анализ', 'warning');
        }

        $news->alias = str_slug($news->id . '-' . $news->name);

        $news->save();

        // Уведомляем компанию что о них написали
        NotifyCompany::notify($news->company_id, $news);



        return redirect(route('news.edit', $news->id));
    }

    public function edit(News $news)
    {
		View::share('page_name', 'Редактирование новости');

		$tags = NewsTag::pluck('name', 'name');

		$companies = Company::pluck('name', 'id')->toArray();
		$companies[0] = 'Не указано';
		ksort($companies);

       // dd($news->textru_data);

        return view('admin.news.form', compact('news', 'tags', 'companies'));
    }

    public function update(Request $request, News $news)
    {
		$this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

        $data['title'] = $data['name'] . ' - Мясной эксперт';

        $news->update($data);
		$news->updateTags($request->get('tags'));

        flash('Новость успешно обновлена', 'success');

        if ($request->get('check')) {
            $news->textru = '';
            $news->textru_uid = Textru::addPost($news->text);
            $news->save();

            flash('Текст отправлен на анализ', 'warning');
        }

        // Уведомляем компанию что о них написали
        NotifyCompany::notify($news->company_id, $news);

        return redirect(route('news.edit', $news->id));
    }

    public function destroy(News $news)
    {
        // Удаляем картинки
        $images = [
            $news->source_image,
            $news->main_image,
            $news->preview,
            $news->on_main
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

		$news->delete();

        flash('Новость успешно удалена', 'success');

        return redirect(route('news.index'));
    }

    public function search(Request $request) {
        $query = trim($request->get('query'));
        $field = trim($request->get('field'));

        $pages = null;

        if ($field == 'date') {
            $pages = News::limit(10)->where('published_at', 'LIKE', $query . '%')->get();
        } else {
            $pages = News::limit(10)->where('name', 'LIKE', '%' . $query . '%')->get();
        }

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
                'data' => route('news.edit', $page->id),
            ];
        }

        return $data;
    }

    public function check($id) {
        $news = News::findOrFail($id);

        if ($news->textru_uid && $news->textru) {
            $view = View::make('admin.partials.textru', ['textru_data' => $news->textru_data]);
            $contents = $view->render();



            $disabled = true;

            $textru_text_unique = \App\Settings::byKey('textru_text_unique');
            $u2 = floatval(str_replace(',', '.', $textru_text_unique->value));

            if (!empty($news) && !$news->published) {
                if($news->textru_uid && $news->textru) {
                    $u1 = floatval($news->textru_data['text_unique']);
                    if ($u1 >= $u2) {
                        $disabled = false;
                    }
                }
            } elseif(!empty($news) && $news->published) {
                $disabled = false;
            }


            return [
                'disabled' => $disabled,
                'check' => true,
                'data' => $contents
            ];
        } else {
            return ['check' => false];
        }
    }
}
