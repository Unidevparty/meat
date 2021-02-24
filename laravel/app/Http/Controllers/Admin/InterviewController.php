<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

use App\ArticleViewStatistic;
use App\Services\StatisticsService as Services;
use \IPS\Member;

use App\InterviewTag;
use App\Interview;
use App\Company;
use App\Textru;
use App\Author;
use App\Helpers\NotifyCompany;
use View;

class InterviewController extends Controller
{
    public function __construct()
	{
        checkPermissions('interview');

		View::share('section_name', 'Интервью');
		View::share('page_name', '');
	}

    public function index(Request $request)
    {
        $start = $request->get('start', '');
        $end = $request->get('end', '');

        View::share('page_name', 'Список');

        $interview = Interview::latest();

        if ($start) {
            $interview->where('published_at', '>=', Carbon::parse($start));
        }

        if ($end) {
            $interview->where('published_at', '<=', Carbon::parse($end));
        }

        $interview = $interview->paginate();

        return view('admin.interview.list', compact('interview', 'start', 'end'));
    }

    public function create()
    {
		View::share('page_name', 'Создание интервью');

		$tags = InterviewTag::pluck('name', 'name');

        $companies = get_companies_list();

        $authors   = Author::pluck('name', 'id');

        return view('admin.interview.form', compact('tags', 'companies', 'authors'));
    }

    public function store(Request $request)
    {
		$this->validate($request, [
            'fio' => 'required'
        ]);

        $data = $request->all();

        // if (!$data['title']) {
            $data['title'] = $data['fio'] .' - ' . $data['post'] . ' - Мясной эксперт';
        // }

        $interview = Interview::create($data);
		$interview->updateTags($request->get('tags'));
        $interview->authors()->sync($data['authors']);

        if ($request->get('check')) {
            $interview->textru = '';
            $interview->textru_uid = Textru::addPost($interview->text);

            flash('Текст отправлен на анализ', 'warning');
        }

        $interview->alias = str_slug($interview->id . '-' . $interview->fio);

        $interview->save();

        // Уведомляем компанию что о них написали
        NotifyCompany::notify($interview->company_id, $interview);

        flash('Интервью успешно создана', 'success');

        return redirect(route('interview.edit', $interview->id));
    }

    public function edit(Interview $interview)
    {
		View::share('page_name', 'Редактирование интервью');

		$tags = InterviewTag::pluck('name', 'name');

        $companies = get_companies_list();

        $authors   = Author::pluck('name', 'id');

        return view('admin.interview.form', compact('interview', 'tags', 'companies', 'authors'));
    }

    public function update(Request $request, Interview $interview)
    {
		$this->validate($request, [
            'fio' => 'required'
        ]);

        $data = $request->all();

        // if (!$data['title']) {
            $data['title'] = 'Интервью. '. $data['fio'] .' - ' . $data['post'] .', ' . $interview->company->name . ' - Мясной эксперт';
        // }

        $interview->update($data);
		$interview->updateTags($request->get('tags'));
        $interview->authors()->sync($data['authors']);

        if ($request->get('check')) {
            $interview->textru = '';
            $interview->textru_uid = Textru::addPost($interview->text);
            $interview->save();

            flash('Текст отправлен на анализ', 'warning');
        }

        // Уведомляем компанию что о них написали
        NotifyCompany::notify($interview->company_id, $interview);


        flash('Интервью успешно обновлена', 'success');

        return redirect(route('interview.edit', $interview->id));
    }

    public function destroy(Interview $interview)
    {
        // Удаляем картинки
        $images = [
            $interview->source_image,
            $interview->main_image,
            $interview->preview,
            $interview->main_slider_source_img,
            $interview->main_slider_big_img,
            $interview->main_slider_sm_img,
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

		$interview->delete();

        flash('Интервью успешно удалена', 'success');

        return redirect(route('interview.index'));
    }

    public function search(Request $request) {
        $query = trim($request->get('query'));
        $field = trim($request->get('field'));

        $pages = null;

        if ($field == 'date') {
            $pages = Interview::limit(10)->where('published_at', 'LIKE', $query . '%')->get();
        } else {
            $pages = Interview::limit(10)->where('fio', 'LIKE', '%' . $query . '%')->get();
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
                'data' => route('interview.edit', $page->id),
            ];
        }

        return $data;
    }

    public function check($id) {
        $interview = Interview::findOrFail($id);

        if ($interview->textru_uid && $interview->textru) {
            $view = View::make('admin.partials.textru', ['textru_data' => $interview->textru_data]);
            $contents = $view->render();

            $disabled = true;

            $textru_text_unique = \App\Settings::byKey('textru_text_unique');
            $u2 = floatval(str_replace(',', '.', $textru_text_unique->value));

            if (!empty($interview) && !$interview->published) {
                if($interview->textru_uid && $interview->textru) {
                    $u1 = floatval($interview->textru_data['text_unique']);
                    if ($u1 >= $u2) {
                        $disabled = false;
                    }
                }
            } elseif(!empty($interview) && $interview->published) {
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
        $interview = Interview::findOrFail($id);

        if ($type) {
            // Удаление конкретной картинки
            $image = trim($interview->$type, '/');

            if (is_file($image)) {
                unlink($image);
            }

            $interview->$type = '';
            $interview->save();

            flash('Картинки кспешно удалены', 'success');

            return redirect(route('interview.edit', $interview->id));
        }

        // Удаляем все картинки
        $images = [
            $interview->source_image,
            $interview->main_image,
            $interview->preview,
            $interview->main_slider_source_img,
            $interview->main_slider_big_img,
            $interview->main_slider_sm_img,
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        $interview->source_image = '';
        $interview->main_image = '';
        $interview->preview = '';
        $interview->main_slider_source_img = '';
        $interview->main_slider_big_img = '';
        $interview->main_slider_sm_img = '';

        $interview->save();



        flash('Картинки кспешно удалены', 'success');

        return redirect(route('interview.edit', $interview->id));
    }

    public function statistics($article_id, Request $request)
    {
        $groupName = Services::getGroups();
        $article = Interview::where('id', $article_id)->first();

        $items = ArticleViewStatistic::article($article_id)
            ->category('interview')
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

        return view('admin.interview.statistic', compact(['data', 'percentArray', 'group_percent', 'allGroups', 'article']));
    }
}
