<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Article;
use App\ArticleTag;
use App\Company;
use App\News;
use App\Author;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use View;


class ArticlesController extends Controller
{
	protected $first_page = 27;
	protected $perpage = 9;

	public function index(Request $request) {
		$articles_data = $this->getArticles($request, $this->first_page);

		$articles = $articles_data['articles'];
		$total = $articles_data['total'];

		$selected_author = $request->get('author');
		$selected_company = $request->get('company');
		$search = $request->get('search');

        if ($search) {
            $selected_author  = null;
            $selected_company = null;
		}
		
		// $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		// dd($spreadsheet);
		// dd(get_class_methods(\IPS\Session::i()));

		$tags_ids = \DB::table('articles_tags')->pluck('article_tag_id');

		$tags = ArticleTag::whereIn('id', $tags_ids)->get();

		$most_viewed = Article::mostViewed();

		// Авторы для сортировки
		// $author_ids = \DB::table('article_author')->whereIn('article_id', $articles_data['all_ids'])->pluck('author_id');
		$author_ids = \DB::table('article_author')->pluck('author_id');

		//$author_ids = \DB::table('article_author')->whereIn('article_id', $articles)->pluck('author_id');
		$authors = Author::whereIn('id', $author_ids)->pluck('name', 'id');



		// Компании для сортировки
		// $companies_ids = Article::whereIn('id', $articles_data['all_ids'])->published()->pluck('company', 'company');
		$companies_ids = Article::published()->pluck('company', 'company');
		$companies = Company::find($companies_ids);

		return view('articles.list', compact(
			'articles',
			'total',
			'tags',
			'most_viewed',
			'selected_author',
			'selected_company',
			'authors',
			'companies',
			'search'
		));
	}

	public function tag(Request $request, $tag) {
		$current_tag = ArticleTag::where('alias', $tag)->first();

		if (!$current_tag || !$current_tag->count()) {
			abort(404);
		}

		$selected_author = $request->get('author');
		$selected_company = $request->get('company');
		$search = $request->get('search');

        $articles_data = $this->getArticles($request, $this->first_page, 0, $tag);

		$articles = $articles_data['articles'];
		$total = $articles_data['total'];

		$most_viewed = Article::mostViewed();

		$tags_ids = \DB::table('articles_tags')->pluck('article_tag_id');

		$tags = ArticleTag::whereIn('id', $tags_ids)->get();

		$tag_alias = $tag;

		// Авторы для сортировки
		$author_ids = \DB::table('article_author')->pluck('author_id');
		// $author_ids = \DB::table('article_author')->whereIn('article_id', $articles)->pluck('author_id');
		$authors = Author::whereIn('id', $author_ids)->pluck('name', 'id');

		// Компании для сортировки
		$companies_ids = Article::published()->pluck('company', 'company');
        $companies = Company::find($companies_ids);

        View::share('title', $current_tag->name);
		View::share('keywords', $current_tag->name);
		View::share('description', $current_tag->name);

		return view('articles.list', compact(
			'articles',
			'tags',
			'total',
			'current_tag',
			'selected_author',
			'selected_company',
			'authors',
			'companies',
			'search',
			'most_viewed',
			'tag_alias'
		));
	}

	public function show_by_id($id)
    {
        $alias = Article::where('id', $id)
            ->value('alias');
        return redirect()->route('articles.show', $alias)->setStatusCode(301);

//		return $this->show('', $id);
	}

	public function show($alias, $id = null)
	{
		$article = null;
		if ($id) {
			$article = Article::where('id', $id);
		} else {
			$article = Article::where('alias', $alias);
		}

		if (!member() || !member()->is_admin()) {
			$article = $article->published();
		}

		$article = $article->first();

		if (!$article || !$article->count()) {
			abort(404);
		}

        \DB::table('articles')->where('id', $article->id)->update(['views' => $article->views + 1]);
        \DB::table('search')->where('searchable_id', $article->id)->where('searchable_type', Article::class)->update(['views' => $article->views + 1]);
        $article->views++; // Не сохраняем т.к. уже сохранили
		

		// $article->views = $article->views + 1;
		// $article->save();

		
		$articles = Article::published()->take(9)->get();

		if (!member() && $article->for_registered) {
			$text_parts = slit_text($article->text, 0, \App\Settings::getByKey('intro_text_percent'));
			$article->text = $text_parts[0];
		}

		View::share('title', $article->title);
		View::share('keywords', $article->keywords);
		View::share('description', $article->description);
		View::share('source_image', $article->main_image);
		View::share('canonical', url(route('articles.show', $article->alias)));



		return view('articles.show', compact('article', 'articles'));
	}

    public function feed()
	{
		$articles = Article::published()->take(20)->get();

		$feed = [];

		foreach ($articles as $article) {
			$text = $article->text;
			if ($article->for_registered) {
				$text_parts = slit_text($text, 0, \App\Settings::getByKey('intro_text_percent'));
				$text = $text_parts[0];
			}
			$feed[] = [
				'title' => $article->name,
				'text' => $text,
				'description' => $article->introtext,
				'image' => url($article->source_image),
				'link' => url(route('articles.show_by_id', $article->id)),
				'author' => $article->author,
				'category' => 'Статьи',
				'date' => $article->published_at->format("D, d M y H:i:s O")
			];
		}

		$meta = getMeta('/articles');
		$title = $meta->title;
		$description = $meta->description;

		return response(view('layouts.rss', compact('feed', 'title', 'description')))->header('Content-Type', 'text/xml');
	}

	public function more(Request $request) {
		$page = $request->get('page', 1);
		$tag_alias = $request->get('tag_alias');
		$offset = $this->first_page + ($page - 1) * $this->perpage;


		$articles = $this->getArticles($request, $this->perpage, $offset, $tag_alias);

		$last_page = ceil(($articles['total'] - $this->first_page) / $this->perpage);
		$next_page = ($page + 1 <= $last_page) ? $page + 1 : false;

		$selected_author = $request->get('author');
		$selected_company = $request->get('company');
		$search = $request->get('search');

		return view('articles.page', [
			'articles' => $articles['articles'],
			'total' => $articles['total'],
			'next_page' => $next_page,
			'last_page' => $last_page,
			'selected_author' => $selected_author,
			'selected_company' => $selected_company,
			'search' => $search,
			'tag_alias' => $tag_alias,
		]);
	}


	protected function getArticles($request, $count, $offset = 0, $tag_alias = '') {

        $selected_author = $request->get('author');
		$selected_company = $request->get('company');
		$search = $request->get('search');

        if ($search) {
            $selected_author  = null;
            $selected_company = null;
        }

		$articles = null;

		if (!$tag_alias) {
			$articles = Article::published();
		} else {
            $current_tag = ArticleTag::where('alias', $tag_alias)->first();

            $article_ids = $current_tag->articles()->published()->select('articles_tags.article_id')->get()->pluck('article_id');
            $articles = Article::whereIn('articles.id', $article_ids);

			//$articles = $current_tag->articles()->published();
		}

		if ($selected_author) {
			$article_ids = Author::find($selected_author)->articles()->pluck('id');
			$articles->whereIn('articles.id', $article_ids);
		}

		if ($selected_company) {
			$articles->where('company', $selected_company);
		}

		if ($search) {
			$articles->where('text', 'like', '%' . $search . '%')->orWhere('name', 'like', '%' . $search . '%');
        }

		return [
            'all_ids'  => $articles->pluck('articles.id'),
			'total'    => $articles->count(),
			'articles' => $articles->skip($offset)->take($count)->get()
		];
	}
}
