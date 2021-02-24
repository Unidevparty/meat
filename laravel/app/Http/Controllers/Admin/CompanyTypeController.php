<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use View;
use App\CompanyType;
use Illuminate\Http\Request;

class CompanyTypeController extends Controller
{
	public function __construct()
	{
		checkPermissions('companies');

		View::share('section_name', 'Профили компаний');
		View::share('page_name', '');
	}

	public function index()
	{
		View::share('page_name', 'Список');

        $types = CompanyType::where('parent_id', 0)->with('childs')->get();

		return view('admin.company_type.list', compact('types'));
	}

	public function create()
	{
		View::share('page_name', 'Создание профиля компании');

        $company_types = CompanyType::where('parent_id', 0)->with('childs')->get();

		return view('admin.company_type.form', compact('company_types'));
	}

	public function store(Request $request)
	{
		$this->validate($request, [
			'name' => 'required'
		]);

		$type = CompanyType::create($request->all());

		flash('Профиль компании успешно создан', 'success');

		return redirect(route('company_type.edit', $type->id));
	}

	public function edit($id)
	{
		$type = CompanyType::findOrFail($id);

        $company_types = CompanyType::where('parent_id', 0)->with('childs')->get();

		View::share('page_name', 'Обновление профиля компании');

		return view('admin.company_type.form', compact('type', 'company_types'));
	}

	public function update(Request $request, $id)
	{
		$type = CompanyType::findOrFail($id);
		$this->validate($request, [
			'name' => 'required'
		]);

		$type->update($request->all());

		flash('Профиль компании успешно обновлен', 'success');

		return redirect(route('company_type.edit', $type->id));
	}

	public function destroy($id)
	{
		$type = CompanyType::findOrFail($id);

		$type->delete();

		flash('Профиль компании успешно удалена', 'success');

		return redirect(route('company_type.index'));
	}
}
