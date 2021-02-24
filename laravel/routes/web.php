<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Auth::routes();

Route::post('login', 'AuthController@login')->name('login');
Route::get('login', function() {
	return redirect('/');
});
Route::any('logout', 'AuthController@logout')->name('logout');

Route::get('/', 'HomeController@index')->name('home');
Route::get('new_sitemap.xml', 'SitemapController@new_sitemap')->name('new_sitemap');
Route::get('sitemap.xml', 'SitemapController@index')->name('sitemap');

Route::get('/email', 'MailController@email')->name('email');

Route::get('/news', 'NewsController@index')->name('news.list');
Route::get('/news/feed', 'NewsController@feed')->name('news.feed');
Route::get('/news/tag', 'NewsController@index')->name('news.tag_list');
Route::get('/news/more', 'NewsController@more')->name('news.more');
Route::get('/news/{id}', 'NewsController@show_by_id')->name('news.show_by_id')->where('id', '[0-9]+');
Route::get('/news/{alias}', 'NewsController@show')->name('news.show');
Route::get('/news/tag/{tag}', 'NewsController@tag')->name('news.tag');


Route::get('/articles', 'ArticlesController@index')->name('articles.list');
Route::get('/articles/feed', 'ArticlesController@feed')->name('articles.feed');
Route::get('/articles/tag', 'ArticlesController@index')->name('articles.tag_list');
Route::get('/articles/more', 'ArticlesController@more')->name('articles.more');
Route::get('/articles/{id}', 'ArticlesController@show_by_id')->name('articles.show_by_id')->where('id', '[0-9]+');
Route::get('/articles/{alias}', 'ArticlesController@show')->name('articles.show');
Route::get('/articles/tag/{tag}', 'ArticlesController@tag')->name('articles.tag');

Route::get('/interviews', 'InterviewsController@index')->name('interviews.list');
Route::get('/interviews/feed', 'InterviewsController@feed')->name('interviews.feed');
Route::get('/interviews/tag', 'InterviewsController@index')->name('interviews.tag_list');
Route::get('/interviews/more', 'InterviewsController@more')->name('interviews.more');
Route::get('/interviews/{id}', 'InterviewsController@show_by_id')->name('interviews.show_by_id')->where('id', '[0-9]+');
Route::get('/interviews/{alias}', 'InterviewsController@show')->name('interviews.show');
Route::get('/interviews/tag/{tag}', 'InterviewsController@tag')->name('interviews.tag');

// Работа
Route::get('/job', 'JobController@index')->name('job.list');
Route::get('/job/more_inside', 'JobController@more_inside')->name('job.more_inside');
Route::get('/job/more', 'JobController@more')->name('job.more');
Route::get('/job/{id}', 'JobController@showById')
    ->name('job.show_by_id')
    ->where('id', '[0-9]+');
Route::get('/job/{alias}', 'JobController@show')->name('job.show');

Route::get('/company', 'CompanyController@index')->name('company.list');
Route::get('/company/more', 'CompanyController@more')->name('company.more');
Route::post('/company/store_review', 'CompanyReviewController@store')->name('company.review_store');
Route::get('/company/review_like/{id}', 'CompanyReviewController@like')->name('company.review_like');
Route::get('/company/review_dislike/{id}', 'CompanyReviewController@dislike')->name('company.review_dislike');
Route::get('/company/{alias}', 'CompanyController@show')->name('company.show');

Route::get('/photogallery', 'PhotogalleryController@index')->name('photogallery.list');
Route::get('/photogallery/more', 'PhotogalleryController@more')->name('photogallery.more');
Route::get('/photogallery/{id}', 'PhotogalleryController@show_by_id')->name('photogallery.show_by_id')->where('id', '[0-9]+');
Route::get('/photogallery/{alias}', 'PhotogalleryController@show')->name('photogallery.show');

Route::get('/get_banners/', 'BannerController@get_banners')->name('banner.get_banners');
Route::get('/banner/{id}', 'BannerController@redirect')->name('banner.url');
Route::get('/banner/get/{position}', 'BannerController@get')->name('banner.get');
Route::post('/comment/store', 'CommentController@store')->name('comment.store');
Route::post('/subscribe', 'MailController@subscribe')->name('subscribe');
Route::get('/subscribe/{code}', 'MailController@subscribe_confirm')->name('subscribe_confirm');
Route::post('/job_email', 'MailController@jobEmail')->name('job_email');
Route::post('/feedback', 'MailController@feedback')->name('feedback');

Route::get('/search', 'SearchController@index')->name('search');

// Route::get('/about', 'PagesController@about')->name('about');
// Route::get('/advertising', 'PagesController@advertising')->name('advertising');
// Route::get('/departments', 'PagesController@departments')->name('departments');
// Route::get('/verification', 'PagesController@verification')->name('verification');

Route::get('/migrate', function() {
	Artisan::call('migrate');
	dd(Artisan::output());
});

Route::get('/searchindex', function() {
	// Artisan::call('searchindex:generate', ['App\Article']);
	Artisan::call('searchindex:generate');
	// Artisan::call('searchindex:generate', ['App\Company']);
	// Artisan::call('searchindex:generate', ['App\Interview']);
	// Artisan::call('searchindex:generate', ['App\Event']);
	dd(Artisan::output());
});

Route::post('/textru', 'Admin\\AdminController@textru')->name('textru');
Route::get('/textru_recheck/{id}', 'Admin\\AdminController@textru_recheck')->name('textru_recheck');
// Route::get('/textru', function() {
// 	return '<form action="/textru" method="post">
// 	<input type="text" name="uid">
// 	<input type="text" name="text_unique">
// 	<input type="text" name="json_result">
// 	<input type="text" name="spell_check">
// 	<input type="submit" value="go">
// </form>';
// })->name('textr2u');

Route::group(['middleware' => 'authAdmin'], function () {
	Route::get('/comment/delete/{id}', 'CommentController@delete')->name('comment.delete');

    Route::get('/company/review_delete/{id}', 'CompanyReviewController@delete')->name('company.review_delete');


	Route::get('admin', 'Admin\\AdminController@index')->name('admin.index');
	Route::get('admin/profile', 'Admin\\AdminController@profile')->name('admin.profile');

	Route::get('/admin/news/{id}/check', 'Admin\\NewsController@check')->name('news.check');
	Route::resource('admin/news', 'Admin\\NewsController', ['except' => ['show']]);
	Route::get('/admin/news/search', 'Admin\\NewsController@search')->name('news.search');

	Route::resource('admin/article', 'Admin\\ArticleController', ['except' => ['show']]);
	Route::get('/admin/article/{id}/check', 'Admin\\ArticleController@check')->name('article.check');
	Route::get('/admin/article/{id}/delete_images/{type?}', 'Admin\\ArticleController@delete_images')->name('article.delete_images');
	Route::get('/admin/article/search', 'Admin\\ArticleController@search')->name('article.search');
	Route::resource('admin/forum_on_main', 'Admin\\ForumOnMainController', ['except' => ['show']]);
    Route::get('/admin/article/{id}/statistics', 'Admin\\ArticleController@statistics')->name('article.statistics');

	Route::resource('admin/interview', 'Admin\\InterviewController', ['except' => ['show']]);
	Route::get('/revenge', 'RevengeController@get');
	Route::get('/admin/interview/{id}/check', 'Admin\\InterviewController@check')->name('interview.check');
	Route::get('/admin/interview/{id}/delete_images/{type?}', 'Admin\\InterviewController@delete_images')->name('interview.delete_images');
	Route::get('/admin/interview/search', 'Admin\\InterviewController@search')->name('interview.search');
    Route::get('/admin/interview/{id}/statistics', 'Admin\\InterviewController@statistics')->name('interview.statistics');

	Route::resource('admin/page', 'Admin\\PageController', ['except' => ['show']]);
	Route::resource('admin/banner', 'Admin\\BannerController', ['except' => ['show']]);

	Route::get('admin/subscriber/search', 'Admin\\SubscriberController@search')->name('subscriber.search');
	Route::get('admin/subscriber/download', 'Admin\\SubscriberController@download')->name('subscriber.download');
	Route::resource('admin/subscriber', 'Admin\\SubscriberController', ['except' => ['show']]);
	
	Route::resource('/admin/comments', 'Admin\\CommentsController', ['except' => ['show']]);

	Route::get('/admin/settings', 'Admin\\SettingsController@index')->name('settings.index');
	Route::post('/admin/settings/save', 'Admin\\SettingsController@save')->name('settings.save');
	Route::get('/admin/meta', 'Admin\\MetaController@index')->name('settings.meta');
	Route::post('/admin/meta', 'Admin\\MetaController@save')->name('settings.meta.save');


	Route::get('/admin/pages', 'Admin\\PagesController@index')->name('pages.index');
	Route::get('/admin/pages/{file}', 'Admin\\PagesController@edit')->name('pages.edit');
	Route::post('/admin/pages/{file}', 'Admin\\PagesController@update')->name('pages.update');

	Route::get('/admin/roles', 'Admin\\SettingsController@roles')->name('settings.roles');
	Route::post('/admin/roles', 'Admin\\SettingsController@roles_save')->name('settings.roles.save');


	Route::resource('admin/job', 'Admin\\JobController', ['except' => ['show']]);
	Route::get('/admin/job/search', 'Admin\\JobController@search')->name('job.search');
	Route::get('/admin/job/signature', 'Admin\\JobController@signature')->name('job.signature');
	Route::post('/admin/job/signature', 'Admin\\JobController@store_signature')->name('job.store_signature');
    Route::resource('admin/job_close', 'Admin\\JobCloseController', ['except' => ['show']]);

    Route::get('/admin/company/search', 'Admin\\CompanyController@search')->name('company.search');
	Route::resource('admin/company', 'Admin\\CompanyController', ['except' => ['show']]);
	Route::resource('admin/company_type', 'Admin\\CompanyTypeController', ['except' => ['show']]);
	Route::resource('admin/companies_filter', 'Admin\\CompaniesFilterController', ['except' => ['show']]);



	Route::get('/admin/photogallery/search', 'Admin\\PhotogalleryController@search')->name('photogallery.search');
	Route::resource('admin/photogallery', 'Admin\\PhotogalleryController', ['except' => ['show']]);


	Route::get('/admin/clear_cache', function() {
		// Чистим кеш
        Cache::flush();
        Artisan::call('view:clear');
        Artisan::call('config:clear');
    	flash('Кеш успешно удален', 'success');
    	return redirect()->back();
	})->name('clear_cache');

	Route::get('/admin/filemanager', function() {
		return view('admin.filemanager');
	})->name('admin.filemanager');


	Route::resource('admin/author', 'Admin\\AuthorController', ['except' => ['show']]);
    Route::get('/admin/author/search', 'Admin\\AuthorController@search')->name('author.search');

    Route::get('/admin/searchlog', 'Admin\\SearchlogController@index')->name('searchlog.index');
    Route::get('/admin/searchlog/download', 'Admin\\SearchlogController@download')->name('searchlog.download');

    Route::get('/admin/searchwords', 'Admin\\SearchwordsController@index')->name('searchwords.index');
    Route::post('/admin/searchwords/save', 'Admin\\SearchwordsController@save')->name('searchwords.save');
});

Route::get('/add_article_statistic', 'ArticleViewStatisticController@add');
Route::get('/update_statistic', 'ArticleViewStatisticController@updateView');
Route::post('/download_statistic/{id}', 'ArticleViewStatisticController@download')->name('download.save');
Route::get('/revenge', 'ArticleViewStatisticController@revenge');

// Маршруты для страниц из бд
$pages = Cache::rememberForever('pages', function() {
    return DB::table('pages')
                    ->where('published', 1)
                    ->where('published_at', '<=', Carbon\Carbon::now())
                    ->orderBy('url', 'desc')
                    ->pluck('url')->toArray();
});

if (!empty($pages)) {
    foreach ($pages as $page) {
        Route::get($page, ['as' => 'pages.show', 'uses' => 'PagesController@showPage']);
    }
}
