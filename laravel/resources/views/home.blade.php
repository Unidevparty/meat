@extends('layouts.main')

@section('page_content')
@php
$banner_b1 = getBanner('B-1');
$banner_b2 = getBanner('B-2');
$banner_h1 = getBanner('H-1');
$banner_a3 = getBanner('A-3');
$banner_t1 = getBanner('T-1');
$banner_t2 = getBanner('T-2');
$banner_p1 = getBanner('P-1');
$banner_k = getBanner('K');
$banner_c1 = getBanner('C-1');
$banner_c2 = getBanner('C-2');
@endphp

<div class="content-row mob-wide">
    <div class="content-slider">
        <ul class="content-slider-itself">

            <?php $articles_data1 = array_splice($articles_data, $articles_on_main_slider); ?>
            <?php $articles_slider = $articles_data; ?>
            @if (count($articles_slider) == $articles_on_main_slider)

            @for ($i = 0; $i < $articles_on_main_slider / 3; $i++) <li class="content-slider-slide">
                <div class="content-cells">
                    <div class="content-cell content-cell-8 content-cell-tab-12">
                        <div class="news-highlight news-highlight--hero thumb-element">
                            <?php $article = $articles_slider[$i*3]; ?>

                            @if ($article['type'] == 'article')
                            <?php $article = $article['data']; ?>
                            <a href="{{ route('articles.show', $article->alias) }}">
                                <img src="{{ $article->big_on_main_slider }}" alt="{{ $article->name }}">
                                <div class="h">{{ $article->name }}</div>
                                <span class="date">
                                    {{ LocalizedCarbon::instance($article->published_at)->formatLocalized('%d %f ‘%y') }}
                                </span>
                                <span class="stats">
                                    <span class="stats-unit">
										<i class="icon icon-views"></i>
										{{ $article->views or 0 }}
									</span>
		
									<span class="stats-unit">                                    
									@if ($article->comments_count == 0)
										
									@else
									<i class="icon icon-comments"></i>
									{{ $article->comments_count }}
									@endif
									</span>
                                </span>
                            </a>
                            @elseif ($article['type'] == 'interview')
                            <?php $article = $article['data']; ?>
                            <a href="{{ route('interviews.show', $article->alias) }}">
                                <span class="interview-label big">Интервью</span>
                                <img src="{{ $article->main_slider_big_img }}" alt="{{ $article->name }}">
                                <div class="h">{{ $article->name }}<br>&#171;{{ $article->company->name }}&#187;</div>
								
								
                                <span class="date">
                                    {{ LocalizedCarbon::instance($article->published_at)->formatLocalized('%d %f ‘%y') }}
                                </span>
                                <span class="stats">
                                    <span class="stats-unit">
										<i class="icon icon-views"></i>
										{{ $article->views or 0 }}
									</span>
		
									<span class="stats-unit">                                    
									@if ($article->comments_count == 0)
										
									@else
									<i class="icon icon-comments"></i>
									{{ $article->comments_count }}
									@endif
									</span>
                                </span>
                            </a>
                            @else
                            <?php $forum = $article['data']; ?>
                            <a href="{{ $forum['url'] }}">
                                <img src="{{ $forum['big_on_main_slider'] }}" alt="{{ $forum['title'] }}">
                                <div class="h">{{ $forum['title'] }} <span class="author">{{ $forum['firstPost']['author']['name'] }}</span></div>

                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="content-cell content-cell-4 content-cell-tab-12">
                        @for ($j = 1; $j <= 2; $j++) <div class="news-highlight thumb-element">
                            <?php $article = $articles_slider[$i*3+$j]; ?>
                            @if ($article['type'] == 'article')
                            <?php $article = $article['data']; ?>
                            <a href="{{ route('articles.show', $article->alias) }}">
                                <img src="{{ $article->sm_on_main_slider }}" alt="{{ $article->name }}">
                                <div class="h">{{ cut_text($article->name, 55) }}</div>
                                <span class="date">
                                    {{ LocalizedCarbon::instance($article->published_at)->formatLocalized('%d %f ‘%y') }}
                                </span>
                                <span class="stats">
                                    <span class="stats-unit">
										<i class="icon icon-views"></i>
										{{ $article->views or 0 }}
									</span>
		
									<span class="stats-unit">                                    
									@if ($article->comments_count == 0)
										
									@else
									<i class="icon icon-comments"></i>
									{{ $article->comments_count }}
									@endif
									</span>
                                </span>
                            </a>

                            @elseif ($article['type'] == 'interview')
                            <?php $article = $article['data']; ?>
                            <a href="{{ route('interviews.show', $article->alias) }}">
                                <span class="interview-label sm">Интервью</span>
                                <img src="{{ $article->main_slider_sm_img }}" alt="{{ $article->name }}">
                                <div class="h">{{ cut_text($article->name, 55) }}</div>								
                                <span class="date">
                                    {{ LocalizedCarbon::instance($article->published_at)->formatLocalized('%d %f ‘%y') }}
                                </span>
                                <span class="stats">
                                    <span class="stats-unit">
										<i class="icon icon-views"></i>
										{{ $article->views or 0 }}
									</span>
		
									<span class="stats-unit">                                    
									@if ($article->comments_count == 0)
										
									@else
									<i class="icon icon-comments"></i>
									{{ $article->comments_count }}
									@endif
									</span>
                                </span>
                            </a>
                            @else
                            <?php $forum = $article['data']; ?>
                            <a href="{{ $forum['url'] }}" class="slider-article-thumb-forum">
                                <span class="forum-label">ФОРУМ</span>
                                <img src="{{ $forum['sm_on_main_slider'] }}" alt="{{ $forum['title'] }}">
                                <div class="h">{{ cut_text($forum['title'], 55) }}</div>
                                <span class="author">{{ $forum['firstPost']['author']['name'] }}</span>
                            </a>
                            @endif
                    </div>
                    @endfor
                </div>
    </div>
    </li>
    @endfor
    @endif
    </ul>
</div>
</div>


<div class="content-row">
    <div class="content-cells">
        <div class="content-cell content-cell-4 content-cell-tab-12">
            <div class="section-header">
                <div class="h">Новости</div>
            </div>
            <div class="news-thumbs" id="news-prime">
                <?php $news2 = $news->splice(3); ?>
                @foreach ($news as $new)
                <div class="news-thumb">
                    <figure>
                        <a href="{{ route('news.show' , $new->alias) }}">
                            <img src="{{ $new->on_main }}" alt="{{ $new->name }}">
                            <span class="date">
                                {{ LocalizedCarbon::instance($new->published_at)->diffForHumans() }}
                            </span>
                            <span class="stats">
								<span class="stats-unit">                                    
									@if ($new->comments_count == 0)
										
									@else
										<i class="icon icon-comments"></i>
										{{ $new->comments_count }}
									@endif
                                </span>
							
                                <span class="stats-unit">
                                    <i class="icon icon-views"></i>
                                    {{ $new->views or 0 }}
                                </span>
                                
                            </span>
                        </a>
                    </figure>
                    <div class="news-thumb-description">
                        <div class="news-thumb-title">
                            <a href="{{ route('news.show' , $new->alias) }}">{{ $new->name }}</a>
                        </div>
                    </div>
                </div>
                @endforeach
				
            </div>
				<br/>
				<a href="{{ route('news.list') }}" class="btn btn-reg btn-red wide mob-show" >все новости</a>
        </div>
        <div class="content-cell content-cell-8 content-cell-tab-12" id="forum-prime">
            <div class="section-header">
                <div class="h">Форум</div>
                @if (member())
                <a href="/forums/discover/unread/" class="btn btn-reg btn-red mob-hide">Непрочитанное</a>
                @endif
                <a href="/forums/" class="btn btn-reg btn-red">перейти в форум</a>
            </div>

            <div class="forum-thumbs">
                <?php
						$topics = \App\Forum::topics();
						$topics2 = array_splice($topics, 0, 3);
					?>
                @foreach ($topics2 as $topic)
                <div class="forum-thumb">
                    <div class="forum-thumb-category">
                        <div class="forum-category-ph bg-red">
                            <i class="icon icon-forum-unity"></i>
                        </div>
                    </div>
                    <div class="forum-thumb-description">
                        <div class="vfix">
                            <a href="{{ $topic['url'] }}" class="h">
                                {{ $topic['title'] }}
                            </a>
                            <p>{{ cut_text(strip_tags($topic['firstPost']['content']), 100) }}</p>
                        </div>
                    </div>
                    <div class="forum-thumb-msgs">
                        <div class="vfix">
                            <i class="icon icon-comments"></i>
                            {{ $topic['posts'] }}
                        </div>
                    </div>
                    <div class="forum-thumb-author">
                        <div class="vfix">
                            <div class="author-module">
                                <div class="author-userpic">
                                    <img src="{{ resize($topic['firstPost']['author']['photoUrl'], 46, 46) }}" width="46" alt="{{ $topic['firstPost']['author']['name'] }}">
                                </div>
                                <div class="author-details">
                                    <a href="{{ $topic['firstPost']['author']['profileUrl'] }}" class="author-name">{{ $topic['firstPost']['author']['name'] }}</a>
                                    <span class="author-date">{{ LocalizedCarbon::instance(Carbon\Carbon::parse($topic['firstPost']['date']))->formatLocalized('%d %f ‘%y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<div class="content-row banners-row">
    <div class="content-cells">

        @if (!empty($banner_b1))
        <div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
            <a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b1->main_image }}" alt="{{ $banner_b1->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b2))
        <div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
            <a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b2->main_image }}" alt="{{ $banner_b2->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b1))
        <div class="mob-additional-banner">
            <a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b1->mobile_image }}" alt="{{ $banner_b1->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b2))
        <div class="mob-additional-banner">
            <a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b2->mobile_image }}" alt="{{ $banner_b2->name }}">
            </a>
        </div>
        @endif
    </div>
</div>

{{-- @if (!empty($banner_a3))
		<div class="content-row banner-through tab-gea">
			<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
<img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
</a>
</div>
@endif --}}

<div class="content-row lap-hide mob-hide">
    <div class="news-thumbs" id="news-tab-additional"></div>
</div>




<div class="content-row">
    <div class="content-cells bottom-banner-container">
        <div class="content-cell content-cell-4 bottom-banner-fix tab-hide">
            <div class="news-thumbs" id="news-additional">
                @foreach ($news2 as $new)
                <div class="news-thumb">
                    <figure>
                        <a href="{{ route('news.show' , $new->alias) }}">
                            <img src="{{ $new->on_main }}" alt="{{ $new->name }}">
                            <span class="date">
                                {{ LocalizedCarbon::instance($new->published_at)->diffForHumans() }}
                            </span>
                            <span class="stats">
                                <span class="stats-unit">                                    
									@if ($new->comments_count == 0)
										
									@else
										<i class="icon icon-comments"></i>
										{{ $new->comments_count }}
									@endif
                                </span>
							
                                <span class="stats-unit">
                                    <i class="icon icon-views"></i>
                                    {{ $new->views or 0 }}
                                </span>
                            </span>
                        </a>
                    </figure>
                    <div class="news-thumb-description">
                        <div class="news-thumb-title">
                            <a href="{{ route('news.show' , $new->alias) }}">{{ $new->name }}</a>
                        </div>
                    </div>
                </div>
                @endforeach

                <a href="{{ route('news.list') }}" class="btn btn-reg btn-red wide">все новости</a>
            </div>
        </div>
        <div class="content-cell content-cell-8 content-cell-tab-12 events-cell">
            <div class="forum-thumbs" id="forum-additional">
                @foreach ($topics as $topic)
                <div class="forum-thumb">
                    <div class="forum-thumb-category">
                        <div class="forum-category-ph bg-red">
                            <i class="icon icon-forum-unity"></i>
                        </div>
                    </div>
                    <div class="forum-thumb-description">
                        <div class="vfix">
                            <a href="{{ $topic['url'] }}" class="h">
                                {{ $topic['title'] }}
                            </a>
                            <p>{{ cut_text(strip_tags($topic['firstPost']['content']), 100) }}</p>
                        </div>
                    </div>
                    <div class="forum-thumb-msgs">
                        <div class="vfix">
                            <i class="icon icon-comments"></i>
                            {{ $topic['posts'] }}
                        </div>
                    </div>
                    <div class="forum-thumb-author">
                        <div class="vfix">
                            <div class="author-module">
                                <div class="author-userpic">
                                    <img src="{{ resize($topic['firstPost']['author']['photoUrl'], 46, 46) }}" width="46" alt="{{ $topic['firstPost']['author']['name'] }}">
                                </div>
                                <div class="author-details">
                                    <a href="{{ $topic['firstPost']['author']['profileUrl'] }}" class="author-name">{{ $topic['firstPost']['author']['name'] }}</a>
                                    <span class="author-date">{{ LocalizedCarbon::instance(Carbon\Carbon::parse($topic['firstPost']['date']))->formatLocalized('%d %f ‘%y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- <div class="section-header events-header">
					<div class="h">Календарь событий</div>
					<a href="" class="btn btn-reg btn-red">открыть календарь</a>
				</div> --}}

            <div class="side-banner-fix bottom-banner-fix">
                <div id="df4fff" style="min-height: 150px; width: 578px; height:555px;position: relative;">
                    <table style="width: 100%;position: absolute;top: 50%;transform: translateY(-50%);">
                        <tbody>
                            <tr>
                                <td style="width: 782px;">
                                    <div class="">
                                        @if (!empty($banner_k))
                                        <a href="https://meat-expert.ru/articles/320" class="banner" target="_blank" rel="nofollow">
                                            <img src="images/ads/1_5.jpg" alt="" class="mob-hide">
                                            <img src="images/ads/1_6.jpg" alt="" class="mob-show">
                                        </a>
                                        @endif
                                    </div>
                                    {{--<p style="text-align: center;"><strong>Здесь скоро будет</strong></p>--}}
                                    {{--<p style="text-align: center;"><strong>Календарь событий</strong></p>--}}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <style media="screen">
                    @media (max-width: 1023px) {
                        #df4fff {
                            /*display: none;*/
                            width: auto !important;
                            height: auto !important;
                            margin-bottom: -70px;
                        }
                    }

                </style>





                {{-- <div class="event-thumbs">
						<div class="event-thumb">
							<a href="">
								<figure>
									<img src="/images/_temp/event_thumb.jpg" alt="">
									<span class="date">
										21 – 22 сентября ‘17
									</span>
								</figure>
							</a>
							<div class="h"><a href="">Международная выставка Ingredients</a></div>
							<div class="location">
								<i class="icon icon-geo"></i>
								Симферополь
							</div>
							<p>Выставка профессионалов мясной индустрии. Ежегодное мероприятие.</p>
						</div>
						<div class="event-thumb">
							<a href="">
								<figure>
									<img src="/images/_temp/event_thumb.jpg" alt="">
									<span class="date">
										21 – 22 сентября ‘17
									</span>
								</figure>
							</a>
							<div class="h"><a href="">Международная выставка Ingredients</a></div>
							<div class="location">
								<i class="icon icon-geo"></i>
								Симферополь
							</div>
							<p>Выставка профессионалов мясной индустрии. Ежегодное мероприятие.</p>
						</div>
						<div class="event-thumb">
							<a href="">
								<figure>
									<img src="/images/_temp/event_thumb.jpg" alt="">
									<span class="date">
										21 – 22 сентября ‘17
									</span>
								</figure>
							</a>
							<div class="h"><a href="">Международная выставка Ingredients</a></div>
							<div class="location">
								<i class="icon icon-geo"></i>
								Симферополь
							</div>
							<p>Выставка профессионалов мясной индустрии. Ежегодное мероприятие.</p>
						</div>
						<div class="event-thumb">
							<a href="">
								<figure>
									<img src="/images/_temp/event_thumb.jpg" alt="">
									<span class="date">
										21 – 22 сентября ‘17
									</span>
								</figure>
							</a>
							<div class="h"><a href="">Международная выставка Ingredients</a></div>
							<div class="location">
								<i class="icon icon-geo"></i>
								Симферополь
							</div>
							<p>Выставка профессионалов мясной индустрии. Ежегодное мероприятие.</p>
						</div>
						<div class="event-thumb">
							<a href="">
								<figure>
									<img src="/images/_temp/event_thumb.jpg" alt="">
									<span class="date">
										21 – 22 сентября ‘17
									</span>
								</figure>
							</a>
							<div class="h"><a href="">Международная выставка Ingredients</a></div>
							<div class="location">
								<i class="icon icon-geo"></i>
								Симферополь
							</div>
							<p>Выставка профессионалов мясной индустрии. Ежегодное мероприятие.</p>
						</div>
						<div class="event-thumb">
							<a href="">
								<figure>
									<img src="/images/_temp/event_thumb.jpg" alt="">
									<span class="date">
										21 – 22 сентября ‘17
									</span>
								</figure>
							</a>
							<div class="h"><a href="">Международная выставка Ingredients</a></div>
							<div class="location">
								<i class="icon icon-geo"></i>
								Симферополь
							</div>
							<p>Выставка профессионалов мясной индустрии. Ежегодное мероприятие.</p>
						</div>
					</div> --}}
                @if (!empty($banner_k))
                <div class="side-banner tab-hide">
                    <a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
						<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}"></a>
                    
                </div>
                @endif
            </div>


            <div class="bottom-banner">
                @if (!empty($banner_k))
                <div class="banner banner-through-add banners-row nobefore">
                    <a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
                    </a>
                </div>
                @endif
                @if (!empty($banner_h1))
                <div class="mob-additional-banner">
                    <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_h1->name }}">
                    </a>
                </div>
                @endif
                @if (!empty($banner_h1))
                <div class="mob-hide">
                    <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_h1->main_image }}" alt="{{ $banner_h1->name }}">
                    </a>
                </div>
                @endif
                @if (!empty($banner_a3))
                <div class="content-section banner lap-hide">
                    <a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="content-row banner-through tab-gea">
    @if (!empty($banner_a3))
    <a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
        <img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
    </a>
    @endif
</div>

{{-- @if (!empty($banner_h1))
<div class="mob-additional-banner">
    <a href="https://us02web.zoom.us/webinar/register/WN_cBBQi3KDThWzG1AY4xwM0A" target="_blank" rel="nofollow">
        <img src="https://meat-expert.ru/ads/prizma/3.jpg" alt="аромадон">
    </a>
</div>
@endif
@if (!empty($banner_h1))
<div class="mob-hide">
    <a href="https://us02web.zoom.us/webinar/register/WN_cBBQi3KDThWzG1AY4xwM0A" target="_blank" rel="nofollow">
        <img src="https://meat-expert.ru/ads/prizma/2.jpg" alt="аромадон">
    </a>
</div>
@endif --}}
<br/>
{{-- <div class="content-row banner-through" style="background-image:url(/uploads/pics/depositphotos-13676301-original.jpg);">
    <div class="banner-through-content">
        <div class="h">
            <span style="color: #FFF800; font-size:80%">НЕЗАВИСИМЫЙ СЕРВИСНЫЙ ЦЕНТР</span><br />
            <span style="color: #FFF800; font-size:80%">ДЛЯ МЯСНОГО И КОЛБАСНОГО ОБОРУДОВАНИЯ</span>
            <small>диагностика, ремонт и пусконаладка</small>

        </div>
        <a href="http://service.meat-expert.ru/" target="_blank" class="btn btn-reg btn-wbg">подробнее</a>
    </div>
</div> --}}

{{-- <div class="content-row banner-through" style="background-image:url(/uploads/pics/depositphotos-13676301-original.jpg);">
		<div class="banner-through-content">
			<div class="h">
			 НЕЗАВИСИМЫЙ СЕРВИСНЫЙ ЦЕНТР<br/>ДЛЯ МЯСНОГО И КОЛБАСНОГО ОБОРУДОВАНИЯ
				<small>диагностика, ремонт и пусконаладка</small>
				ВАШЕГО ОБОРУДОВАНИЯ
			</div>
			<a href="http://oborud.meat-expert.ru/" target="_blank" class="btn btn-reg btn-wbg">подробнее</a>
		</div>
	</div> --}}

<div class="content-row">
    <div class="section-header">
        <div class="h">Статьи</div>
        <a href="{{ route('articles.list') }}" class="btn btn-reg btn-red">в раздел статьи</a>
    </div>
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            <div class="articles-thumbs">

                @foreach ($articles_data1 as $article)
                @if ($article['type'] == 'article')
                @include('articles.thumb', ['article' => $article['data']])
                @else
                @include('partials.forum_thumb_on_main', ['forum' => $article['data']])
                @endif
                @endforeach


                @if (!empty($banner_c1))
                <div class="mob-additional-banner">
                    <a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
                    </a>
                </div>
                @endif
                @if (!empty($banner_c2))
                <div class="mob-additional-banner">
                    <a href="{{ $banner_c2->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
                    </a>
                </div>
                @endif
            </div>
        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @if (!empty($banner_c1))
            <a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_c1->main_image }}" alt="{{ $banner_c1->name }}">
            </a>
            @endif
            @if (!empty($banner_c2))
            <a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_c2->main_image }}" alt="{{ $banner_c2->name }}">
            </a>
            @endif
        </div>
    </div>
</div>

<div class="content-row content-row-white content-row-interviews">
    <div class="section-header">
        <div class="h">Интервью</div>
        <a href="{{ route('interviews.list') }}" class="btn btn-reg btn-red">все интервью</a>
    </div>
    <div class="content-cells">
        @foreach ($interviews as $interview)
        <div class="content-cell content-cell-6 content-cell-tab-12">
            <a href="{{ route('interviews.show', $interview->alias) }}" class="interview-thumb">
                <figure>
                    <img src="{{ $interview->preview }}" alt="{{ $interview->fio }}">
                </figure>
                <div class="interview-thumb-details">
                    <div class="person-name">{{ $interview->fio }}</div>
                    <div class="person-role">{{ $interview->post }}</div>
                    <div class="person-role">{{ $interview->company ? '' . $interview->company->name : '' }}</div>
                    <blockquote>
                        <p>{!! $interview->introtext !!}</p>
                    </blockquote>
                    <footer>
                        <div class="btn btn-reg btn-blue">подробнее</div>
                        <span class="stats">
                            <span class="stats-unit">
                                <i class="icon icon-views"></i>
                                {{ $interview->views or 0 }}
                            </span>
                            <span class="stats-unit">
                                <i class="icon icon-comments"></i>
                                {{ $interview->comments_count or 0 }}
                            </span>
                        </span>
                    </footer>
                </div>
            </a>
        </div>
        @endforeach
    </div>
</div>

<div class="content-row">
    <div class="content-cells content-cell-rngr">
        <div class="content-cell content-cell-4m cell-polls">
            {{-- <div class="wbox">
					<div class="section-h">
						<div class="h">Опрос</div>
					</div>
					<div class="h3">Смогут ли фермеры прокормить Россию?</div>
					<form class="poll-form">
						<ul class="poll-list">
							<li>
								<label>
									<input type="radio" name="rad-01">
									<span>Смогут, если им не будут мешать бандиты и чиновники</span>
								</label>
							</li>
							<li>
								<label>
									<input type="radio" name="rad-01">
									<span>Не смогут, потому что у оптовикови сетей продукты дешевле</span>
								</label>
							</li>
							<li>
								<label>
									<input type="radio" name="rad-01">
									<span>Это не их задача, фермеры производят не вал, а уникальные продукты</span>
								</label>
							</li>
						</ul>
						<input type="submit" value="ответить" class="btn btn-reg btn-red wide">
						<div class="more-small tar"><a href="">Архив опросов</a></div>
					</form>
				</div> --}}
            <table style="height: 100%;" width="100%">
                <tbody>
                    <tr>

                        <td style="width: 782px;">
                            <div class="">
                                @if (!empty($banner_k))
                                <a href="https://meat-expert.ru/articles/178" class="banner" target="_blank" rel="nofollow">
                                    <img src="images/ads/2_1.jpg" alt="" class="mob-hide">
                                    <img src="images/ads/2_2.jpg" alt="" class="mob-show">
                                </a>
                                @endif
                            </div>



                            {{--<p style="text-align: center;"><strong>Здесь скоро будет</strong></p>--}}
                            {{--<p style="text-align: center;"><strong>Каталог компаний</strong></p>--}}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="content-cell content-cell-8m">
            <div class="wbox">
                <div class="section-header">
                    <div class="h">Горячие вакансии</div>
					
                </div>
                <div class="discussion-thumbs">
                    @foreach ($jobs as $job)
                    <div class="discussion-thumb">
                        <figure>
                            @if ($job->company->logo)
                            <img src="{{ resize($job->company->logo, 46, 46, false) }}" alt="{{ $job->company->name }}">
                            @endif
                        </figure>
                        <div class="discussion-thumb-description">
                            <div class="h">
                                <span class="date">{{ $job->published_at ? LocalizedCarbon::instance($job->published_at)->formatLocalized('%d %f ‘%y') : '' }}</span>

                                @if (!$job->our)
                                <a href="{{ route('job.show', $job->alias) }}" class="author">{{ $job->company->name }}</a>
                                @endif
                            </div>
                            <div class="h4">
                                <a href="{{ route('job.show', $job->alias) }}">{{ $job->name }}</a>
                            </div>

                            <div class="job_info">
                                @if ($job->city)
                                <span class="vac-stats-unit first"><i class="icon icon-geo"></i> {{ $job->city }}</span>
                                <span class="vac-stats-unit"><i class="icon icon-income"></i> <b>{{ number_format($job->zarplata, 0, ',', ' ') }}<span class="rub">i</span></b> {{ $job->zp_options }}.</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
				<br/>
				<a href="/job" class="btn btn-reg btn-red wide">все вакансии</a>
            </div>
			
        </div>









        <div class="content-cell content-cell-aside">
            @if (!empty($banner_p1))
            <a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}" class="mob-hide">
                <img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}" class="mob-show">
            </a>
            @endif
        </div>
    </div>
</div>

@include('partials.brands_carusel')

<div class="content-row">
    <div class="section-header">
        <div class="h">Галереи</div>
        <a href="{{ route('photogallery.list') }}" class="btn btn-reg btn-red">Все галереи</a>
    </div>
    <div class="galleries-layout">
        <div class="galleries-layout-in">
            @php
            $i = 1;
            @endphp
            @foreach ($photogalleries as $gallery)
            @php
            $big = ($i++ % 4) < 2; @endphp <div class="gallery-thumb thumb-element {{ $big ? 'gallery-thumb-bigger' : '' }}">
                <a href="{{ route('photogallery.show', $gallery->alias) }}">
                    <img src="{{ $big ? $gallery->home_image_1 : $gallery->home_image_2 }}" alt="{{ $gallery->name }}">
                    <div class="h">{{ $gallery->name }}</div>
                    <span class="stats">
                        <span class="stats-unit">
                            <i class="icon icon-views"></i>
                            {{ $gallery->views }}
                        </span>
                        <span class="stats-unit">
                            <i class="icon icon-comments"></i>
                            {{ $gallery->comments_count or 0 }}
                        </span>
                    </span>
                </a>
        </div>
        @endforeach
    </div>
</div>
</div>

{{-- <div class="content-row banner-through" style="background-image:url(https://meat-expert.ru/laravel-filemanager/uploads/pics/depositphotos-13676301-original.jpg);">
		<div class="banner-through-content">
			<div class="h">
				<small>НЕЗАВИСИМЫЙ СЕРВИСНЫЙ ЦЕНТР</small>
				ДЛЯ МЯСНОГО И КОЛБАСНОГО ОБОРУДОВАНИЯ<br/>
				диагностика, ремонт и пусконаладка
			</div>
			<a href="http://oborud.meat-expert.ru/" class="btn btn-reg btn-wbg">подробнее</a>
		</div>
	</div> --}}

<div class="content-row">
    <div class="content-cells content-cell-rngr cell-blog-thumbs">
        <div class="content-cell content-cell-8m">
            <div class="wbox">
                <div class="section-header">
                    <div class="h">Блоги — Читаемое</div>
                    <a href="/forums/blogs/" class="btn btn-reg btn-red">все блоги</a>
                </div>
                <div class="blog-thumbs">
                    @foreach (App\Blog::posts(5) as $post)
                    <div class="blog-thumb">
                        <div class="h"><a href="{{ $post['url'] }}">{{ $post['title'] }}</a></div>
                        <p>{{ cut_text(strip_tags($post['entry']), 150) }}</p>
                        <footer>
                            <div class="author-details">
                                <figure><img src="{{ resize($post['author']['photoUrl'], 46, 46) }}" alt="{{ $post['author']['name'] }}"></figure>
                                <a href="{{ $post['author']['profileUrl'] }}" class="author">{{ $post['author']['name'] }}</a>
                                <span class="date">{{ LocalizedCarbon::instance(Carbon\Carbon::parse($post['date']))->formatLocalized('%d %f ‘%y') }}</span>
                            </div>
                            <span class="stats">
                                <span class="stats-unit">
                                    <i class="icon icon-views"></i>
                                    {{ $post['views'] }}
                                </span>
                                <span class="stats-unit">
                                    <i class="icon icon-comments"></i>
                                    {{ $post['comments'] }}
                                </span>
                            </span>
                        </footer>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="content-cell content-cell-4m cell-bloggers">
            <div class="wbox">
                <div class="section-h">
                    <div class="h">Лучшие блоггеры</div>
                </div>
                <div class="discussion-thumbs bloggers-thumbs">
                    @foreach (App\Blog::bloggers(5) as $blogger)
                    <div class="discussion-thumb">
                        <figure><img src="{{ resize($blogger['photoUrl'], 46, 46) }}" alt="{{ $blogger['name'] }}"></figure>
                        <div class="discussion-thumb-description">
                            <div class="h">
                                <a href="{{ $blogger['profileUrl'] }}" class="author">{{ $blogger['name'] }}</a>
                            </div>
                            {{-- <div class="h4">
										<a href="">Что делать, если мясо напало на вас в магазине</a>
									</div> --}}
                            <span class="date">{{ LocalizedCarbon::instance(Carbon\Carbon::parse($blogger['joined']))->formatLocalized('%d %f ‘%y') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @if (!empty($banner_t1))
            <a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_t1->main_image }}" alt="{{ $banner_t1->name }}">
            </a>
            @endif

            @if (!empty($banner_t2))
            <a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_t2->main_image }}" alt="{{ $banner_t2->name }}">
            </a>
            @endif
        </div>
        @if (!empty($banner_t1))
        <a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow" class="mob-show mob-additional-banner"><img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}"></a>
        @endif
        @if (!empty($banner_t2))
        <a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow" class="mob-show mob-additional-banner"><img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}"></a>
        @endif
    </div>
</div>
@endsection
