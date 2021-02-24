<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\ArticleViewStatistic;
use App\Services\StatisticsService as Services;
use \IPS\Member;

use App\ArticleTag;
use App\Article;
use App\Company;
use App\Textru;
use App\Author;
use App\Helpers\NotifyCompany;
use View;



class ArticleController extends Controller
{
    public function __construct()
    {
        checkPermissions('articles');

        View::share('section_name', 'Статьи');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        $start = $request->get('start', '');
        $end = $request->get('end', '');

        View::share('page_name', 'Список');

       // $articles = Article::latest()->paginate();

        $articles = Article::latest();

        if ($start) {
            $articles->where('published_at', '>=', Carbon::parse($start));
        }

        if ($end) {
            $articles->where('published_at', '<=', Carbon::parse($end));
        }

        $articles = $articles->paginate();

        return view('admin.articles.list', compact('articles', 'start', 'end'));
    }

    public function create()
    {
        View::share('page_name', 'Создание статьи');

        $tags = ArticleTag::pluck('name', 'name');

        $companies = get_companies_list();

        $authors   = Author::pluck('name', 'id');

        return view('admin.articles.form', compact('tags', 'companies', 'authors'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

        //if (!$data['title']) {
            $data['title'] = $data['name'] . ' - Мясной эксперт';
        // }

        $article = Article::create($data);
        $article->updateTags($request->get('tags'));

        $article->authors()->sync($data['authors']);

        if ($request->get('check')) {
            $article->textru = '';
            $article->textru_uid = Textru::addPost($article->text);


            flash('Текст отправлен на анализ', 'warning');
        }

        $article->alias = str_slug($article->id . '-' . $article->name);

        $article->save();

        // Уведомляем компанию что о них написали
        NotifyCompany::notify($article->company, $article);

        flash('Статья успешно создана', 'success');

        return redirect(route('article.edit', $article->id));
    }

    public function search(Request $request) {
        $query = trim($request->get('query'));
        $field = trim($request->get('field'));

        $pages = null;

        if ($field == 'date') {
            $pages = Article::limit(10)->where('published_at', 'LIKE', $query . '%')->get();
        } else {
            $pages = Article::limit(10)->where('name', 'LIKE', '%' . $query . '%')->get();
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
                'data' => route('article.edit', $page->id),
            ];
        }

        return $data;
    }



    public function edit(Article $article)
    {
        $tags = ArticleTag::pluck('name', 'name');

        $companies = get_companies_list();
        $authors   = Author::pluck('name', 'id');

        return view('admin.articles.form', compact('article', 'tags', 'companies', 'authors'));
    }

    public function update(Request $request, Article $article)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

        // if (!$data['title']) {
            $data['title'] = $data['name'] . ' - Мясной эксперт';
        // }

        $article->update($data);
        $article->updateTags($request->get('tags'));

        $article->authors()->sync($data['authors']);

        if ($request->get('check')) {
            $article->textru = '';
            $article->textru_uid = Textru::addPost($article->text);
            $article->save();

            flash('Текст отправлен на анализ', 'warning');
        }

        // Уведомляем компанию что о них написали
        NotifyCompany::notify($article->company, $article);

        flash('Статья успешно обновлена', 'success');

        return redirect(route('article.edit', $article->id));
    }

    public function destroy(Article $article)
    {
        // Удаляем картинки
        $images = [
            $article->source_image,
            $article->main_image,
            $article->preview,
            $article->preview_big,
            $article->on_main,
            $article->big_on_main_slider,
            $article->sm_on_main_slider
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        $article->delete();

        flash('Статья успешно удалена', 'success');

        return redirect(route('article.index'));
    }

    public function check($id) {
        $article = Article::findOrFail($id);

        if ($article->textru_uid && $article->textru) {
            $view = View::make('admin.partials.textru', ['textru_data' => $article->textru_data]);
            $contents = $view->render();

            $disabled = true;

            $textru_text_unique = \App\Settings::byKey('textru_text_unique');
            $u2 = floatval(str_replace(',', '.', $textru_text_unique->value));

            if (!empty($article) && !$article->published) {
                if($article->textru_uid && $article->textru) {
                    $u1 = floatval($article->textru_data['text_unique']);
                    if ($u1 >= $u2) {
                        $disabled = false;
                    }
                }
            } elseif(!empty($article) && $article->published) {
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

    public function delete_images($id, $type = '')
    {
        $article = Article::findOrFail($id);

        if ($type) {
            // Удаление конкретной картинки
            $image = trim($article->$type, '/');

            if (is_file($image)) {
                unlink($image);
            }

            $article->$type = '';
            $article->save();

            flash('Картинки кспешно удалены', 'success');

            return redirect(route('article.edit', $article->id));
        }

        // Удаляем все картинки
        $images = [
            $article->source_image,
            $article->main_image,
            $article->preview,
            $article->preview_big,
            $article->on_main,
            $article->big_on_main_slider,
            $article->sm_on_main_slider
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        $article->source_image = '';
        $article->main_image = '';
        $article->preview = '';
        $article->preview_big = '';
        $article->on_main = '';
        $article->big_on_main_slider = '';
        $article->sm_on_main_slider = '';

        $article->save();



        flash('Картинки кспешно удалены', 'success');

        return redirect(route('article.edit', $article->id));
    }

    public function statistics($article_id, Request $request)
    {
        $groupName = Services::getGroups();
        $article = Article::where('id', $article_id)->first();

        $items = ArticleViewStatistic::article($article_id)
            ->category('articles')
            ->get();

        $data = [
            '25%' => [],
            '50%' => [],
            '75%' => [],
            '100%' => [],
        ];

        $group_percent = [];
        $allGroups = [];
        $percentArray = [];
        $totalViews = 0;

        foreach($items as $value){

            $member = Member::load($value->user_id);

            $group_percent[$value->percent][$groupName[$member->member_group_id]] += $value->views;

            $totalViews += $value->views;
            $allGroups[$groupName[$member->member_group_id]]['count'] += 1;
            $percentArray[$value->percent]['views'] += $value->views;
            $percentArray[$value->percent][$groupName[$member->member_group_id]] += 1;

            $data[$value->percent][] = [
                'name' => $member->real_name ?? 'Гость',
                'group' => $groupName[$member->member_group_id],
                'views' => $value->views,
                'date' => $value->updated_at->toDateString(),
                'ip' => $value->ip
            ];

        }

        foreach ($data as $percent => $item) {
            $percentArray[$percent]['percent'] =  round(Services::getPercent($percentArray[$percent]['views'], $totalViews)).'%';

            foreach ($group_percent[$percent] as $group => $count) {
                $allGroups[$group]['group_percent'] = round(Services::getPercent($allGroups[$group]['count'], $items->count()), 2).'%';
            }
            
        }

        return view('admin.articles.statistic', compact(['data', 'percentArray', 'group_percent', 'allGroups', 'article']));
    }

}
