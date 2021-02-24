<?php

namespace App\Http\Controllers;

use App\Job;
use App\News;
use App\CompanyType;
use App\Company;
use Illuminate\Http\Request;

class JobController extends Controller
{
	protected $first_page = 15;
	protected $perpage = 9;

    public function index(Request $request)
    {
		$published_at = $request->get('published_at');
		$selected_city = $request->get('city');
		$selected_type = $request->get('type');
		$zarplata = $request->get('zarplata');

		$jobs = Job::published()->nofixed();
		$fixed = Job::published()->fixed();

		if ($published_at) {
			$jobs->orderBy('published_at', $published_at);
			$fixed->orderBy('published_at', $published_at);

		}
		if ($zarplata) {
			$jobs->orderBy('zarplata', $zarplata);
			$fixed->orderBy('zarplata', $zarplata);
		}
		if ($selected_city) {
			$jobs->where('city', $selected_city);
			$fixed->where('city', $selected_city);
		}
		if ($selected_type) {
			$jobs->where('company_type_id', $selected_type);
			$fixed->where('company_type_id', $selected_type);
		}

		if (!$published_at && !$zarplata) {
			$jobs->latest();
			$fixed->latest();
		}

		$fixed = $fixed->take($this->first_page)->get();
		$jobs = $jobs->take($this->first_page - $fixed->count())->get();


		$jobs = $fixed->concat($jobs);

		// dd($fixed, $jobs);

		$more_news = News::published()->take(5)->get();

		$total = Job::published()->count();

		$cities = Job::published()->groupBy('city')->pluck('city');

		$types = CompanyType::pluck('name', 'id');

        return view('job.list', [
			'total' => 123,
			'first_page' => $this->first_page,
			'more_news' => $more_news,
			'jobs' => $jobs,
			'total' => $total,
			'cities' => $cities,
			'types' => $types,
			'published_at' => $published_at,
			'selected_city' => $selected_city,
			'selected_type' => $selected_type,
			'zarplata' => $zarplata,
			'more_url' => route('job.more', [
				'page' => 1,
				'published_at' => $published_at,
				'city' => $selected_city,
				'type' => $selected_type,
				'zarplata' => $zarplata,
			])
		]);
    }

    /**
     * Переадресация с id на alias
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function showById($id)
    {
        $alias = Job::where('id', $id)
            ->value('alias');
        return redirect()->route('job.show', $alias)->setStatusCode(301);

//        return $this->show('', $id);
    }

	public function show($alias)
	{
		$job = Job::where('alias', $alias)->published()->first();

		if (!$job) {
			return abort(404);
		}

		$job->views += 1;
		$job->save();

		$jobs = Job::published()->nofixed()->where('id', '<>', $job->id)->latest()->take(3)->get();
		$total = Job::published()->nofixed()->count();

		return view('job.show', [
			'job' => $job,
			'jobs' => $jobs,
			'total' => $total
		]);
	}

	public function more(Request $request) {
		$page = (int) $request->get('page', 1);
		$published_at = $request->get('published_at');
		$selected_city = $request->get('city');
		$selected_type = $request->get('type');
		$zarplata = $request->get('zarplata');



		$offset = $this->first_page + ($page - 1) * $this->perpage;


		$jobs = Job::published()->latest();

		$jobs = Job::published();

		if ($published_at || $zarplata) {
			$jobs->orderBy('fixed', 'desc');
		}

		if ($published_at) {
			$jobs->orderBy('published_at', $published_at);
		}
		if ($zarplata) {
			$jobs->orderBy('zarplata', $zarplata);
		}
		if ($selected_city) {
			$jobs->where('city', $selected_city);
		}
		if ($selected_type) {
			$jobs->where('company_type_id', $selected_type);
		}

		if (!$published_at && !$zarplata) {
			$jobs->latest();
		}

		$total = $jobs->count();

		$jobs = $jobs->skip($offset)->take($this->perpage)->get();



		$last_page = ceil(($total - $this->first_page) / $this->perpage);
		$next_page = ($page + 1 <= $last_page) ? $page + 1 : false;

		return view('job.page', [
			'jobs' => $jobs,
			'total' => $total,
			'next_page' => $next_page,
			'last_page' => $last_page,
			'more_url' => route('job.more', [
				'page' => $next_page,
				'published_at' => $published_at,
				'city' => $selected_city,
				'type' => $selected_type,
				'zarplata' => $zarplata,
			])
		]);
	}

	public function more_inside(Request $request) {
		$first_page = 3;
		$perpage = 3;

		$page = (int) $request->get('page', 1);

		$offset = $first_page + ($page - 1) * $perpage;


		$jobs = Job::published()->nofixed()->latest()->skip($offset)->take($perpage)->get();
		$total = Job::published()->nofixed()->count();



		$last_page = ceil(($total - $first_page) / $perpage);
		$next_page = ($page + 1 <= $last_page) ? $page + 1 : false;

		return view('job.page', [
			'jobs' => $jobs,
			'total' => $total,
			'next_page' => $next_page,
			'last_page' => $last_page,
			'more_url' => route('job.more_inside', ['page' => $next_page])
		]);
	}
}
