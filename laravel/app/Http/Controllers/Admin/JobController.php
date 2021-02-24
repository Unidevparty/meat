<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Job;
use App\JobClose;
use App\Company;
use App\CompanyType;
use App\IPB;
use App\Settings;
use Illuminate\Http\Request;
use Carbon\Carbon;
use View;

class JobController extends Controller
{
	protected $zp_options = [
		'+ по результатам собеседования',
		'+ % от продаж',
		'+ KPI',
		'+ Соцпакет',
	];

    public function __construct()
	{
        checkPermissions('job');

		View::share('section_name', 'Работа');
		View::share('page_name', '');
	}

    public function index(Request $request)
    {
        $start = $request->get('start', '');
        $end = $request->get('end', '');

        View::share('page_name', 'Список');

        $jobs = Job::latest();

        if ($start) {
            $jobs->where('published_at', '>=', Carbon::parse($start));
        }

        if ($end) {
            $jobs->where('published_at', '<=', Carbon::parse($end));
        }

        $jobs = $jobs->paginate();

        return view('admin.job.list', compact('jobs', 'start', 'end'));
    }

    public function create()
    {
        View::share('page_name', 'Создание вакансии');

		$jobCloses = JobClose::all()->pluck('name', 'id');
		$companies = Company::all()->pluck('name', 'id');
		$company_types = CompanyType::all()->pluck('name', 'id');
		$groups = IPB::groups();


		return view('admin.job.form', [
			'companies' => $companies,
			'jobCloses' => $jobCloses,
			'company_types' => $company_types,
			'zp_options' => $this->zp_options,
			'groups' => $groups
		]);
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

		$data['zp_options'] = implode('  ', $data['zp_options']);
		$data['visibility'] = '|' . implode('|', $data['groups']) . '|';

        $job = Job::create($data);

        flash('Вакансия успешно создана', 'success');

        return redirect(route('job.edit', $job->id));
    }

    public function edit(Job $job)
    {
		View::share('page_name', 'Редактирование вакансии');



		$jobCloses = JobClose::all()->pluck('name', 'id');
		$companies = Company::all()->pluck('name', 'id');
		$company_types = CompanyType::all()->pluck('name', 'id');
		$groups = IPB::groups();

		// dd($groups);

        return view('admin.job.form', [
			'companies' => $companies,
			'jobCloses' => $jobCloses,
			'company_types' => $company_types,
			'zp_options' => $this->zp_options,
			'job' => $job,
			'groups' => $groups
		]);
    }

    public function update(Request $request, Job $job)
    {
		$this->validate($request, [
            'name' => 'required'
        ]);

        $data = $request->all();

		// if (!$data['title']) {
            $data['title'] = $data['name'] . ' - Мясной эксперт';
        // }

		$data['zp_options'] = implode('  ', $data['zp_options']);
		$data['visibility'] = '|' . implode('|', $data['groups']) . '|';

        $job->update($data);

        flash('Вакансия успешно обновлена', 'success');

        return redirect(route('job.edit', $job->id));
    }

    public function destroy(Job $job)
    {
        // Удаляем картинки
        // $images = [
        //     $interview->source_image,
        //     $interview->main_image,
        //     $interview->preview
        // ];

        // foreach ($images as $image) {
        //     $image = trim($image, '/');
        //     if (is_file($image)) {
        //         unlink($image);
        //     }
        // }

		$job->delete();

        flash('Вакансия успешно удалена', 'success');

        return redirect(route('job.index'));
    }

    public function search(Request $request) {
        $query = trim($request->get('query'));
        $field = trim($request->get('field'));

        $pages = null;

        if ($field == 'date') {
            $pages = Job::limit(10)->where('published_at', 'LIKE', $query . '%')->get();
        } else {
            $pages = Job::limit(10)->where('name', 'LIKE', '%' . $query . '%')->get();
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
                'data' => route('job.edit', $page->id),
            ];
        }

        return $data;
    }

	public function signature()
	{
		View::share('page_name', 'Подпись и пример резюме');

		$signature = Settings::getByKey('signature');
		$example = Settings::getByKey('example');

		return view('admin.job.signature', [
			'signature' => $signature,
			'example' => $example
		]);
	}

	public function store_signature(Request $request)
	{

		$signature = $request->get('signature');
		$settings_signature = Settings::firstOrNew(['key' => 'signature']);
		$settings_signature->value = $signature;
		$settings_signature->save();

		$example = $request->get('example');
		$settings_example = Settings::firstOrNew(['key' => 'example']);
		$settings_example->value = $example;
		$settings_example->save();

		flash('Настройки успешно сохранены', 'success');

		return redirect(route('job.signature'));
	}
}
