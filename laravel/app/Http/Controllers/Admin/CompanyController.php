<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use View;
use Illuminate\Http\Request;
use App\Company;
use App\CompanyType;
use Carbon\Carbon;

class CompanyController extends Controller
{
	protected $types = [
		'представительство иностранной компании',
		'российская компания',
	];
    public function __construct()
    {
        checkPermissions('companies');

        View::share('section_name', 'Компании');
        View::share('page_name', '');
    }

    public function index(Request $request)
    {
        $start = $request->get('start', '');
        $end = $request->get('end', '');

        View::share('page_name', 'Список');

       // $articles = Article::latest()->paginate();

        $companies = Company::latest();

        if ($start) {
            $companies->where('published_at', '>=', Carbon::parse($start));
        }

        if ($end) {
            $companies->where('published_at', '<=', Carbon::parse($end));
        }

        $companies = $companies->paginate();

        return view('admin.company.list', compact('companies', 'start', 'end'));
    }

    public function create()
    {
		View::share('page_name', 'Создание компании');

		$holdings = Company::where('is_holding', 1)->pluck('name', 'id')->toArray();
		$holdings[0] = 'Не указано';
		ksort($holdings);

		// $profiles = CompanyType::pluck('name', 'name')->toArray();
        $profiles = CompanyType::where('parent_id', 0)->get();

        return view('admin.company.form', [
			'types' => $this->types,
			'holdings' => $holdings,
			'profiles' => $profiles,
		]);
    }

	public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            // 'logo' => 'required',
            // 'type' => 'required'
        ]);

		$data = $request->all();

		$data['description'] = $data['description'] ?: $data['introtext'];
		$data['title'] = $data['name'] . ' | Каталог компаний – Мясной эксперт';

        $company = Company::create($data);
		$company->updateTypes($request->get('types'));
        $company->savePhotos($request);

        flash('Компания успешно создана', 'success');

        if ($request->ajax()) {
            return ['redirect' => route('company.edit', $company->id)];
        }

        return redirect(route('company.edit', $company->id));
    }

    public function edit(Company $company)
    {

        // dd($company->gallery);
        View::share('page_name', 'Обновление компании');

		$holdings = Company::where('is_holding', 1)->pluck('name', 'id')->toArray();
		$holdings[0] = 'Не указано';
		ksort($holdings);

        // $profiles = CompanyType::pluck('name', 'name')->toArray();
        $profiles = CompanyType::where('parent_id', 0)->with('childs')->get();

        $current_profiles = $company->types->pluck('id')->toArray();

		return view('admin.company.form', [
			'company' => $company,
			'types' => $this->types,
			'holdings' => $holdings,
			'profiles' => $profiles,
            'current_profiles' => $current_profiles,
		]);
    }

    public function update(Request $request, Company $company)
    {
        $this->validate($request, [
			'name' => 'required',
            // 'type' => 'required'
        ]);

		$data = $request->all();

		$data['description'] = $data['description'] ?: $data['introtext'];
		$data['title'] = $data['name'] . ' | Каталог компаний – Мясной эксперт';

		$company->update($data);
		$company->updateTypes($request->get('types'));
        $company->savePhotos($request);

        flash('Компания успешно обновлена', 'success');

        if ($request->ajax()) {
            return ['redirect' => route('company.edit', $company->id)];
        }

        return redirect(route('company.edit', $company->id));
    }

    public function destroy(Company $company)
    {
        // Удаляем картинки
        $images = [
            $company->logo
        ];

        foreach ($images as $image) {
            $image = trim($image, '/');
            if (is_file($image)) {
                unlink($image);
            }
        }

        $company->delete();

        flash('Комапния успешно удалена', 'success');

        return redirect(route('company.index'));
    }



    public function search(Request $request) {
        $query = trim($request->get('query'));
        $field = trim($request->get('field'));

        $pages = null;

        $pages = Company::limit(10);

        if ($field == 'date') {
            $pages = $pages->where('published_at', 'LIKE', $query . '%');
        } else {
            $pages = $pages->where('name', 'LIKE', '%' . $query . '%');
        }

        $pages = $pages->get();

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
                'data' => route('company.edit', $page->id),
            ];
        }

        return $data;
    }
}
