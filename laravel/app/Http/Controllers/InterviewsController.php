<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Interview;
use App\Article;
use App\Company;
use App\Author;
use App\News;
use View;


use App\InterviewTag;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class InterviewsController extends Controller
{
	protected $first_page = 27;
	protected $perpage = 9;

    public function index(Request $request) {
		$interviews = Interview::where('published_at', '<=', Carbon::now())->where('published', 1);

		$selected_author = '';
		$selected_company = '';
		$order = '';

		if ($selected_author = $request->get('author')) {
			$interview_ids = Author::find($selected_author)->interviews()->pluck('id');
			$interviews->whereIn('interviews.id', $interview_ids);
		}

		if ($selected_company = $request->get('company')) {
			$interviews->where('company_id', $selected_company);
		}

		if ($order = $request->get('order')) {
			if ($order == 'date') {
				$interviews->orderBy('published_at', 'desc');
			} elseif ($order == 'popular') {
				$interviews->orderBy('views', 'desc');
			}

		} else {
			$interviews->orderBy('published_at', 'desc');
		}

		$total = $interviews->count();
		$interviews = $interviews->take($this->first_page)->get();

		$tags = InterviewTag::all();

		// Авторы для сортировки
		// $author_ids = \DB::table('author_interview')->whereIn('interview_id', $interviews)->pluck('author_id');
		$author_ids = \DB::table('author_interview')->pluck('author_id');
		$authors = Author::whereIn('id', $author_ids)->pluck('name', 'id');

		// Компании для сортировки
		$companies_ids = Interview::pluck('company_id', 'company_id');
		$companies = Company::find($companies_ids);
        
		return view('interview.list', compact(
			'interviews',
			'tags',
			'order',
			'selected_author',
			'selected_company',
			'authors',
			'companies',
			'total'
		));
	}

	public function tag(Request $request, $tag) {
		$tag_alias = $tag;
		$current_tag = InterviewTag::where('alias', $tag)->first();


		if (!$current_tag || !$current_tag->count()) {
			abort(404);
		}

		$interviews = $current_tag->interviews()->where('published_at', '<=', Carbon::now())->where('published', 1);

		$selected_author = '';
		$selected_company = '';
		$order = '';

		if ($selected_author = $request->get('author')) {
			$interview_ids = Author::find($selected_author)->interviews()->pluck('id');
			$interviews->whereIn('interviews.id', $interview_ids);
		}

		if ($selected_company = $request->get('company')) {
			$interviews->where('company_id', $selected_company);
		}

		if ($order = $request->get('order')) {
			if ($order == 'date') {
				$interviews->orderBy('published_at', 'asc');
			} elseif ($order == 'popular') {
				$interviews->orderBy('views', 'asc');
			}

		}

		$total = $interviews->count();
		$interviews = $interviews->take($this->first_page)->get();

		$tags = InterviewTag::all();

		// Авторы для сортировки
		// $author_ids = \DB::table('author_interview')->whereIn('interview_id', $interviews)->pluck('author_id');
		$author_ids = \DB::table('author_interview')->pluck('author_id');
		$authors = Author::whereIn('id', $author_ids)->pluck('name', 'id');

		// Компании для сортировки
		$companies_ids = Interview::pluck('company_id', 'company_id');
		$companies = Company::find($companies_ids);

		return view('interview.list', compact(
			'interviews',
			'tags',
			'current_tag',
			'order',
			'selected_author',
			'selected_company',
			'authors',
			'companies',
			'total',
			'tag_alias'
		));
	}

	public function show_by_id($id)
    {
        $alias = Interview::where('id', $id)
            ->value('alias');
        return redirect()->route('interviews.show', $alias)->setStatusCode(301);

//		return $this->show('', $id);
	}


	public function show($alias, $id = null)
	{
		$interview = null;
		if ($id) {
			$interview = Interview::where('id', $id);
		} else {
			$interview = Interview::where('alias', $alias);
		}



		if (!member() || !member()->is_admin()) {
			$interview = $interview->published();
		}

		$interview = $interview->first();

		if (!$interview || !$interview->count()) {
			abort(404);
		}

        \DB::table('interviews')->where('id', $interview->id)->update(['views' => $interview->views + 1]);
        \DB::table('search')->where('searchable_id', $interview->id)->where('searchable_type', Interview::class)->update(['views' => $interview->views + 1]);
        $interview->views++; // Не сохраняем т.к. уже сохранили

		// $interview->views = $interview->views + 1;
		// $interview->save();

		// $more_articles = Article::published()->take(5)->get();

		$articles = Article::published()->take(9)->get();


		View::share('title', $interview->title);
		View::share('keywords', $interview->keywords);
		View::share('description', $interview->description);
		View::share('source_image', $interview->main_image);
		View::share('canonical', url(route('interviews.show', $interview->alias)));

		if (!member() && $interview->for_registered) {
			$text_parts = slit_text($interview->text, 0, \App\Settings::getByKey('intro_text_percent'));
			$interview->text = $text_parts[0];
		}

		return view('interview.show', compact('interview', 'articles'));
	}

    public function feed()
	{
		$interviews = Interview::published()->take(20)->get();

		$feed = [];

		foreach ($interviews as $interview) {
			$text = $interview->text;
			if ($interview->for_registered) {
				$text_parts = slit_text($text, 0, \App\Settings::getByKey('intro_text_percent'));
				$text = $text_parts[0];
			}

			$feed[] = [
				'title' => $interview->name,
				'text' => $text,
				'description' => $interview->introtext,
				'image' => url($interview->source_image),
				'link' => url(route('interviews.show_by_id', $interview->id)),
				'category' => 'Интервью',
				'date' => $interview->published_at->format("D, d M y H:i:s O")
			];
		}

		$meta = getMeta('/interviews');
		$title = $meta->title;
		$description = $meta->description;

		return response(view('layouts.rss', compact('feed', 'title', 'description')))->header('Content-Type', 'text/xml');
	}


	public function more(Request $request) {
		$page = $request->get('page', 1);
		$tag_alias = $request->get('tag_alias');
		$offset = $this->first_page + ($page - 1) * $this->perpage;

		$order = $request->get('order');
		$selected_author = $request->get('selected_author');
		$selected_company = $request->get('selected_company');

		$interviews = null;
		$total = 0;

		if ($tag_alias) {
			$current_tag = InterviewTag::where('alias', $tag)->first();


			if (!$current_tag || !$current_tag->count()) {
				return '';
			}

			$interviews = $current_tag->interviews()->where('published_at', '<=', Carbon::now())->where('published', 1);
		} else {
			$interviews = Interview::where('published_at', '<=', Carbon::now())->where('published', 1);
		}

		if ($selected_author) {
			$interview_ids = Author::find($selected_author)->interviews()->pluck('id');
			$interviews->whereIn('interviews.id', $interview_ids);
		}

		if ($selected_company) {
			$interviews->where('company_id', $selected_company);
		}

		if ($order) {
			if ($order == 'date') {
				$interviews->orderBy('published_at', 'desc');
			} elseif ($order == 'popular') {
				$interviews->orderBy('views', 'desc');
			}
		} else {
			$interviews->orderBy('published_at', 'desc');
		}

		$total = $interviews->count();
		$interviews = $interviews->skip($offset)->take($this->first_page)->get();



		$last_page = ceil(($total - $this->first_page) / $this->perpage);
		$next_page = ($page + 1 <= $last_page) ? $page + 1 : false;

		$url = [
			'page' => $next_page,
			'tag_alias' => $tag_alias,
			'order' => $order,
			'selected_author' => $selected_author,
			'selected_company' => $selected_company,
		];

		return view('interview.page', [
			'total' => $total,
			'interviews' => $interviews,
			'last_page' => $last_page,
			'next_page' => $next_page,
			'url' => route('interviews.more', $url),
		]);
	}
}
