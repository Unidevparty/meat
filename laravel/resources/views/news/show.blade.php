@extends('layouts.main', [
'title' => $news->title,
'description' => $news->description,
'keywords' => $news->keywords,
])

@section('page_content')
@php
$banner_news = getBanner('news');
$banner_a3 = getBanner('A-3');
@endphp

<div class="content-row" itemscope itemtype="https://schema.org/NewsArticle">
    <?php $canonical = $_SERVER['REQUEST_URI']; ?>
    <link itemprop="mainEntityOfPage" href="{!! $canonical !!}" />
    <meta itemprop="author" content="Мясной Эксперт">
    @if (hasPermissions('news'))
    <a href="{{ route('news.edit', $news->id) }}" class="edit_btn">Изменить</a>
    @endif
<!--     <ul class="breadcrumbs">
        <li><a href="/">Главная</a></li>
        <li><a href="{{ route('news.list') }}">Новости</a></li>
        <li>{{ $news->name }}</li>
    </ul> -->
    <ul itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumbs">
            <li itemprop="itemListElement" itemscope
          itemtype="https://schema.org/ListItem"><a itemprop="item" href="/"><span itemprop="name">Главная</span></a><meta itemprop="position" content="1" /></li>
            <li itemprop="itemListElement" itemscope
          itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ route('news.list') }}"><span itemprop="name">Статьи</span></a><meta itemprop="position" content="2" /></li>
            <li itemprop="itemListElement" itemscope
          itemtype="https://schema.org/ListItem"><span itemprop="name">{{ $news->name }}</span><meta itemprop="position" content="3" /></li>
        </ul>
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            <article class="article">
                <header class="article-header news-header">
                    <div class="article-header-in">
                        <h1><span itemprop="headline name">{{ $news->name }}</span></h1>
                        <div class="tags">
                            @foreach ($news->tags as $tag)
                            <a href="{{ route('news.tag', $tag->alias) }}"><small class="tag">{{ $tag->name }}</small></a>
                            @endforeach
                        </div>
                        @if ($news->main_image)
                        <img src="{{ $news->main_image }}">
                        <link itemprop="image" href="{{ $news->main_image }}">
                        @endif
                    </div>
                </header>
                <?php
					$text_parts = slit_text($news->text, 0, 50);
				?>
                <meta itemprop="description" content="{{ $news->name }}">
                <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
                    <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                        <img itemprop="url image" src="https://meat-expert.ru/images/logo.png" alt="logo" title="logo" style="display:none;"/>
                    </div>
                    <meta itemprop="name" content="Мясной Эксперт">
                    <meta itemprop="telephone" content="">
                    <meta itemprop="address" content="Россия">
                </div>
                <div itemprop="articleBody">
                    {!! $text_parts[0] !!}

                @if (!empty($banner_news))
                <div class="mob-additional-banner">
                    <a href="{{ $banner_news->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_news->mobile_image }}" alt="{{ $banner_news->name }}">
                    </a>
                </div>
                @endif

                {!! $text_parts[1] !!}
                 </div>
                <footer class="article-footer">
                    <div class="author-module">
                        <div class="author-details">
                            <span class="author-date">{{ LocalizedCarbon::instance($news->published_at)->formatLocalized('%d %f ‘%y') }}</span>
                            <meta itemprop="datePublished" content="{!! $news->published_at->format('Y-m-d') !!}">
                            <meta itemprop="dateModified" content="{!! $news->published_at->format('Y-m-d') !!}">
                        </div>
                    </div>
                    <div class="share">
                        <div class="label">Поделиться:</div>
                        <div class="share-cnt">
                            @include('partials.share')
                        </div>
                    </div>
                    <div class="views">
                        <i class="icon icon-views"></i>
                        {{ $news->views or 0 }}
                    </div>

                </footer>

            </article>
        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @if (!empty($banner_news))
            <a href="{{ $banner_news->fake_url }}" target="_blank" rel="nofollow" class="banner side-box mob-hide">
                <img src="{{ $banner_news->main_image }}" alt="{{ $banner_news->name }}">
            </a>
            @endif

            @include('partials.subscribe_side_form')
        </div>
    </div>
</div>


<div class="content-row banners-row">
    <div class="content-cells">
        @php
        $banner_b1 = getBanner('B-1');
        $banner_b2 = getBanner('B-2');
        @endphp

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
        <div class="mob-additional-banner nobefore">
            <a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b1->mobile_image }}" alt="{{ $banner_b1->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b2))
        <div class="mob-additional-banner nobefore">
            <a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b2->mobile_image }}" alt="{{ $banner_b2->name }}">
            </a>
        </div>
        @endif

    </div>
</div>

@if (!empty($banner_a3))
<div class="content-row banner-through tab-gea">
    <a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
        <img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
    </a>
</div>
@endif



<div class="content-row">
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            @include('partials.comments', ['page' => $news, 'type' => 'news'])
            @php
            $banner_k = getBanner('K');
            $banner_h1 = getBanner('H-1');
            @endphp

            @if (!empty($banner_k))
            <div class="mob-additional-banner">
                <a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
                </a>
            </div>
            @endif
            @if (!empty($banner_h1))
            <div class="content-section banner mob-hide">
                <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_h1->main_image }}" alt="{{ $banner_h1->name }}">
                </a>
            </div>

            <div class="mob-additional-banner nobefore">
                <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_h1->name }}">
                </a>
            </div>
            @endif
            @if ($banner_a3)
            <div class="content-section banner lap-hide">
                <a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
                </a>
            </div>
            @endif

            <div class="content-section">
                <div class="section-header">
                    <div class="h">Статьи</div>
                    <a href="{{ route('articles.list') }}" class="btn btn-reg btn-red">смотреть все</a>
                </div>
                <div class="articles-thumbs">
					<!--noindex-->
                    @foreach ($articles as $article)
                    @include('articles.thumb', ['article' => $article])
                    @endforeach
					<!--/noindex-->

                    @php
                    $banner_c1 = getBanner('C-1');
                    $banner_c2 = getBanner('C-2');
                    $banner_p1 = getBanner('P-1');
                    @endphp

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
            @if (!empty($banner_p1))
            <div class="mob-additional-banner">
                <a href="{{ $banner_p1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
                </a>
            </div>
            @endif

           {{--  @if (!empty($banner_h1))
			<div class="mob-additional-banner">
				<a href="https://us02web.zoom.us/webinar/register/WN_cBBQi3KDThWzG1AY4xwM0A" target="_blank" rel="nofollow">
					<img src="https://meat-expert.ru/ads/prizma/3.jpg" alt="аромадон">
				</a>
			</div>
			@endif --}}
			@if (!empty($banner_h1))
			<div class="mob-hide">
				<a href="https://us02web.zoom.us/webinar/register/WN_cBBQi3KDThWzG1AY4xwM0A" target="_blank" rel="nofollow">
					<img src="https://meat-expert.ru/ads/prizma/2.jpg" alt="аромадон">
				</a>
			</div>
			@endif
			<!--noindex-->
            @include('blog.most_viewed')
			<!--/noindex-->

            @php
            $banner_t1 = getBanner('T-1');
            $banner_t2 = getBanner('T-2');
            @endphp


            @if (!empty($banner_t1))
            <div class="mob-show mob-additional-banner">
                <a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
                </a>
            </div>
            @endif
			<!--noindex-->
            @include('partials.forum_in_pages')
			<!--/noindex-->
            @if (!empty($banner_t2))
            <div class="mob-show mob-additional-banner">
                <a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
                </a>
            </div>
            @endif
        </div>
        <div class="content-cell content-cell-aside mob-hide">
			<!--noindex-->
            <div class="side-box">
                @include('partials.more_news')
            </div>
			<!--/noindex-->
            @php
            $banner_k = getBanner('K');
            $banner_c1 = getBanner('C-1');
            $banner_c2 = getBanner('C-2');
            $banner_p1 = getBanner('P-1');
            $banner_t1 = getBanner('T-1');
            $banner_t2 = getBanner('T-2');
            @endphp
            @if (!empty($banner_k))
            <a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}"></a>
            @endif
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
            @if (!empty($banner_p1))
            <a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}">
            </a>
            @endif
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
    </div>
</div>

@include('partials.brands_carusel')

@endsection
