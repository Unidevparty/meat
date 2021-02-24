<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CompanyHolding;
use View;

class CompanyHoldingController extends Controller
{
    public function __construct()
	{
		checkPermissions('companies');

		View::share('section_name', 'Холдинги');
		View::share('page_name', '');
	}

    public function index()
    {
    	View::share('page_name', 'Список');

        $holdings = CompanyHolding::paginate();

        return view('admin.company.holdings', compact('holdings'));
    }


    public function create()
    {
        View::share('page_name', 'Создание холдинга');

        return view('admin.company.holdings_form');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $holding = CompanyHolding::create($request->all());

        flash('Холдинг успешно создан', 'success');

        return redirect(route('company_holding.edit', $holding->id));
    }

    public function edit($id)
    {
        $holding = CompanyHolding::findOrFail($id);

        View::share('page_name', 'Обновление холдинга');

        return view('admin.company.holdings_form', compact('holding'));
    }

    public function update(Request $request, $id)
    {
        $holding = CompanyHolding::findOrFail($id);
        $this->validate($request, [
            'name' => 'required'
        ]);

        $holding->update($request->all());

        flash('Холдинг успешно обновлен', 'success');

        return redirect(route('company_holding.edit', $holding->id));
    }

    public function destroy($id)
    {
        $holding = CompanyHolding::findOrFail($id);

		$holding->delete();

		flash('Холдинг успешно удален', 'success');

		return redirect(route('company_holding.index'));
    }
}
