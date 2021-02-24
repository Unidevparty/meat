<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use View;
use Illuminate\Http\Request;

use App\Company;
use App\CompanyType;
use App\CompaniesFilter;

class CompaniesFilterController extends Controller
{
	public function __construct()
	{
		checkPermissions('companies');

		View::share('section_name', 'Фильтры компаний');
		View::share('page_name', '');
	}

	public function index()
	{
		View::share('page_name', 'Список');

        $companies_filters = CompaniesFilter::paginate();

		return view('admin.companies_filter.list', compact('companies_filters'));
	}

	public function create()
	{
		View::share('page_name', 'Создание фильтра компаний');

        $profiles = CompanyType::where('parent_id', 0)->with('childs')->get();

        $countries_map = Company::countries_map();

		return view('admin.companies_filter.form', compact('profiles', 'countries_map'));
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required'
		]);

		$companies_filter = CompaniesFilter::create($request->all());

		$companies_filter->set_filter_string();

		flash('Фильтр компаний успешно создан', 'success');

		return redirect(route('companies_filter.edit', $companies_filter->id));
	}

	public function edit(CompaniesFilter $companies_filter)
	{
        $profiles = CompanyType::where('parent_id', 0)->with('childs')->get();

        $countries_map = Company::countries_map();

		View::share('page_name', 'Обновление фильтра компаний');

		return view('admin.companies_filter.form', compact(
            'companies_filter',
            'profiles',
            'countries_map'
        ));
	}

	public function update(Request $request, CompaniesFilter $companies_filter)
	{
		$this->validate($request, [
			'name' => 'required'
		]);

		$companies_filter->update($request->all());
		$companies_filter->set_filter_string();

		flash('Фильтр компаний успешно обновлен', 'success');

		return redirect(route('companies_filter.edit', $companies_filter->id));
	}

	public function destroy(CompaniesFilter $companies_filter)
	{
		$companies_filter->delete();

		flash('Фильтр компаний успешно удален', 'success');

		return redirect(route('companies_filter.index'));
	}
}
