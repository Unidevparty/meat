<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use View;
use App\JobClose;
use Illuminate\Http\Request;

class JobCloseController extends Controller
{
	public function __construct()
    {
        checkPermissions('job');

        View::share('section_name', 'Причины закрытия вакансии');
        View::share('page_name', '');
    }

    public function index()
    {
		View::share('page_name', 'Список');

		$jobCloses = JobClose::paginate();

		return view('admin.jobclose.list', compact('jobCloses'));
    }

    public function create()
    {
		View::share('page_name', 'Создание причины закрытия вакансии');

        return view('admin.jobclose.form');
    }

	public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required'
        ]);

        $job_close = JobClose::create($request->all());

        flash('Причина закрытия вакансии успешно создана', 'success');

        return redirect(route('job_close.edit', $job_close->id));
    }

    public function edit($id)
    {
		$job_close = JobClose::findOrFail($id);

        View::share('page_name', 'Обновление причины закрытия вакансии');

        return view('admin.jobclose.form', compact('job_close'));
    }

    public function update(Request $request, $id)
    {
		$job_close = JobClose::findOrFail($id);
        $this->validate($request, [
			'name' => 'required'
        ]);

        $job_close->update($request->all());

        flash('Причина закрытия вакансии успешно обновлена', 'success');

        return redirect(route('job_close.edit', $job_close->id));
    }

    public function destroy($id)
    {
		$job_close = JobClose::findOrFail($id);

        $job_close->delete();

        flash('Причина закрытия вакансии успешно удалена', 'success');

        return redirect(route('job_close.index'));
    }
}
