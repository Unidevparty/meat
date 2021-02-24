<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\News;
use App\NewsTag;
use App\Article;
use Carbon\Carbon;
use View;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
	protected $first_page = 39;
	protected $perpage = 9;
	public function index()
	{
		$news = News::published()->take($this->first_page)->get();

		$total = News::published()->count();

		$tags = NewsTag::all();

		
		return view('news.list', compact('news', 'tags', 'total'));
	}

	public function tag($tag)
	{
		$tag_alias = $tag;
		$current_tag = NewsTag::where('alias', $tag)->first();

		if (!$current_tag || !$current_tag->count()) {
			abort(404);
		}

		$news = $current_tag->news()->published()->take($this->first_page)->get();
		$total = $current_tag->news()->published()->count();

		$tags = NewsTag::all();

		return view('news.list', compact(
			'news',
			'tags',
			'current_tag',
			'total',
			'tag_alias'
		));
	}

	public function show_by_id($id)
    {
        $alias = News::where('id', $id)
            ->value('alias');
        return redirect()->route('news.show', $alias)->setStatusCode(301);

//		return $this->show('', $id);
	}
	public function show($alias, $id = null)
	{
		$news = null;
		if ($id) {
			$news = News::where('id', $id);
		} else {
			$news = News::where('alias', $alias);
		}

		if (!member() || !member()->is_admin()) {
			$news = $news->published();
		}

		$news = $news->first();

		if (!$news || !$news->count()) {
			abort(404);
		}

        \DB::table('news')->where('id', $news->id)->update(['views' => $news->views + 1]);
        \DB::table('search')->where('searchable_id', $news->id)->where('searchable_type', News::class)->update(['views' => $news->views + 1]);
        $news->views++; // Не сохраняем т.к. уже сохранили

		// $news->views = $news->views + 1;
		// $news->save();


		View::share('title', $news->title);
		View::share('keywords', $news->keywords);
		View::share('description', $news->description);
		View::share('source_image', $news->main_image);
		View::share('canonical', url(route('news.show', $news->alias)));

		$articles = Article::published()->take(9)->get();

		return view('news.show', compact('news', 'articles'));
	}

	public function feed()
	{
		$news = News::published()->take(20)->get();

		$feed = [];

		foreach ($news as $news_item) {
			$feed[] = [
				'title' => $news_item->name,
				'text' => $news_item->text,
				'description' => $news_item->introtext,
				'image' => url($news_item->source_image),
				'link' => url(route('news.show_by_id', $news_item->id)),
				'category' => 'Новости',
				'date' => $news_item->published_at->format("D, d M y H:i:s O")
			];
		}


		$meta = getMeta('/news');
		$title = $meta->title;
		$description = $meta->description;

		return response(view('layouts.rss', compact('feed', 'title', 'description')))->header('Content-Type', 'text/xml');
	}


	public function more(Request $request) {
		$page = (int) $request->get('page', 1);
		$tag_alias = $request->get('tag_alias');
		$offset = $this->first_page + ($page - 1) * $this->perpage;

		$news = null;
		$total = 0;

		if ($tag_alias) {
			$current_tag = NewsTag::where('alias', $tag_alias)->first();

			$news = $current_tag->news()->published()->skip($offset)->take($this->perpage)->get();
			$total = $current_tag->news()->published()->count();
		} else {
			$news = News::published()->skip($offset)->take($this->perpage)->get();
			$total = News::published()->count();
		}


		$last_page = ceil(($total - $this->first_page) / $this->perpage);
		$next_page = ($page + 1 <= $last_page) ? $page + 1 : false;

		return view('news.page', [
			'news' => $news,
			'total' => $total,
			'next_page' => $next_page,
			'last_page' => $last_page,
			'tag_alias' => $tag_alias,
		]);
	}
}
