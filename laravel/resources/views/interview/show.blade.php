@extends('layouts.main', [
'title' => $interview->title,
'description' => $interview->description,
'keywords' => $interview->keywords,
])

@section('page_content')
@php
$banner_interview = getBanner('interview');
$banner_a3 = getBanner('A-3');
$banner_b1 = getBanner('B-1');
$banner_b2 = getBanner('B-2');
$banner_h1 = getBanner('H-1');
$banner_k = getBanner('K');
$banner_c1 = getBanner('C-1');
$banner_c2 = getBanner('C-2');
$banner_p1 = getBanner('P-1');
$banner_t1 = getBanner('T-1');
$banner_t2 = getBanner('T-2');
@endphp
@php
    $id = $interview->id;
@endphp
<div itemscope itemtype="https://schema.org/BlogPosting">
<div class="content-row">
    <?php $canonical = $_SERVER['REQUEST_URI']; ?>
    <link itemprop="mainEntityOfPage" href="{!! $canonical !!}" />
    <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
        <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
            <img itemprop="url image" src="https://meat-expert.ru/images/logo.png" alt="logo" title="logo" style="display:none;"/>
        </div>
        <meta itemprop="name" content="Мясной Эксперт">
        <meta itemprop="telephone" content="">
        <meta itemprop="address" content="Россия">
    </div>
    @if (hasPermissions('interview'))
    <a href="{{ route('interview.edit', $interview->id) }}" class="edit_btn">Изменить</a>
    @endif
    <ul itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumbs">
        <li itemprop="itemListElement" itemscope
      itemtype="https://schema.org/ListItem"><a itemprop="item" href="/"><span itemprop="name">Главная</span></a><meta itemprop="position" content="1" /></li>
        <li itemprop="itemListElement" itemscope
      itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ route('interviews.list') }}"><span itemprop="name">Интервью</span></a><meta itemprop="position" content="2" /></li>
        <li itemprop="itemListElement" itemscope
      itemtype="https://schema.org/ListItem"><span itemprop="name">{{ $interview->name }}</span><meta itemprop="position" content="3" /></li>
    </ul>
    <div class="content-cells" id="statistic_calculation">
        <div class="content-cell content-cell-main">
            <div class="article">
                <div class="interview-page-header">
                    <figure>
                        <img src="{{ $interview->main_image }}" class="img-rnd" alt="{{ $interview->name }}" width="228">
                        <link itemprop="image" href="{{ $interview->main_image }}" />
                    </figure>
                    <div class="dsc">
                        <h1><small>Интервью</small> <span itemprop="headline name">{{ $interview->fio }}</span></h1>
                        <div class="company">
                            @if ( $interview->company->logo )
                            <img src="{{ resize($interview->company->logo, 80, 80, 0) }}" alt="{{ $interview->company->name }}">
                            @endif
                            <b>{{ $interview->post }}<br /> {{ $interview->company->name }}</b>
                        </div>
                        <div class="title-quote"><span itemprop="description">{{ $interview->quote }}</span></div>
                    </div>
                </div>
                <ul class="tag-list article-tag-list">
                    @foreach ($interview->tags as $tag)
                    <li><a href="{{ route('interviews.tag', $tag->alias) }}">{{ $tag->name }}</a></li>
                    @endforeach
                </ul>

                <?php
						$text_parts = slit_text($interview->text, 3);
					?>
                {!! $text_parts[0] !!}



            </div>
        </div>


        <div class="content-cell content-cell-aside mob-hide">


            @php
            $banner_interview = getBanner('interview');
            @endphp
            @if (!empty($banner_interview))
            <a href="{{ $banner_interview->fake_url }}" target="_blank" rel="nofollow" class="banner">
                <img src="{{ $banner_interview->main_image }}" alt="{{ $banner_interview->name }}">
            </a>
            <br>
            @endif


            @include('partials.subscribe_side_form')
        </div>
    </div>
</div>
@if (!empty($banner_interview))
<div class="mob-additional-banner">
    <a href="{{ $banner_interview->fake_url }}" target="_blank" rel="nofollow">
        <img src="{{ $banner_interview->mobile_image }}" alt="{{ $banner_interview->name }}">
    </a>
</div>
@endif

<div class="content-row banners-row">
    <div class="content-cells">
        @if ($banner_b1)
        <div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
            <a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b1->main_image }}" alt="{{ $banner_b1->name }}">
            </a>
        </div>
        @endif
        @if ($banner_b2)
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
            <div class="article">
                <div itemprop="articleBody">
                    {!! $text_parts[1] !!}
                </div>
                @if (!member() && $interview->for_registered)
                <div class="blockmessage">
                    <div class="in">
                        <div class="icn-placeholder">
                            <i class="icon icon-blocked"></i>
                        </div>
                        <div class="msg">Полная версия интервью доступна только зарегистрированным пользователям</div>
                        <a href="" class="btn btn-reg btn-header-personal" data-pop-link="login">Вход <span class="mob-hide">/ регистрация</span></a>
                    </div>
                </div>
                @endif

                
                <footer class="article-footer">
                    @if ($interview->authors()->count())
                    @foreach ($interview->authors as $author)
                    <div class="author-module">
                        <div class="author-userpic">
                            <img src="{{ resize($author->photo, 44, 44) }}" alt="{{ $author->name }}" width="44">
                        </div>
                        <div class="author-details">
                            <span class="author-name" itemprop="author">{{ $author->name }}</span>
                            <span class="author-date">{{ LocalizedCarbon::instance($interview->published_at)->formatLocalized('%d %f ‘%y') }}</span>
                            <meta itemprop="datePublished" content="{!! $interview->published_at->format('Y-m-d') !!}">
                            <meta itemprop="dateModified" content="{!! $interview->published_at->format('Y-m-d') !!}">
                        </div>
                    </div>
                    @endforeach
                    @endif
                    <div class="share">
                        <div class="label">Поделиться:</div>
                        <div class="share-cnt">
                            @include('partials.share')
                        </div>
                    </div>
                    <div class="views">
                        <i class="icon icon-views"></i>
                        {{ $interview->views }}
                    </div>

                </footer>
				@if ($banner_k)
                <div class="mob-additional-banner">
                    <a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
                    </a>
                </div>
                @endif
            </div>

            @include('partials.comments', ['page' => $interview, 'type' => 'interviews'])

            @if ($banner_h1)
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

                    @if ($banner_c1)
                    <div class="mob-additional-banner">
                        <a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
                            <img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
                        </a>
                    </div>
                    @endif
                    @if ($banner_c2)
                    <div class="mob-additional-banner">
                        <a href="{{ $banner_c2->fake_url }}" target="_blank" rel="nofollow">
                            <img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @if ($banner_p1)
            <div class="mob-additional-banner">
                <a href="{{ $banner_p1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
                </a>
            </div>
            @endif

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
			<!--noindex-->
            @include('blog.most_viewed')
			<!--/noindex-->

            @if ($banner_t1)
            <div class="mob-show mob-additional-banner">
                <a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
                </a>
            </div>
            @endif
			<!--noindex-->
            @include('partials.forum_in_pages')
			<!--/noindex-->

            @if ($banner_t2)
            <div class="mob-show mob-additional-banner">
                <a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
                </a>
            </div>
            @endif

        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @include('partials.subscribe_side_form')
			<!--noindex-->
            <div class="side-box">
                @include('partials.more_news')
            </div>
			<!--/noindex-->
            @if ($banner_k)
            <a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}"></a>            
            @endif
            @if ($banner_c1)
            <a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_c1->main_image }}" alt="{{ $banner_c1->name }}">
            </a>
            @endif
            @if ($banner_c2)
            <a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_c2->main_image }}" alt="{{ $banner_c2->name }}">
            </a>
            @endif
            @if ($banner_p1)
            <a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}">
            </a>
            @endif
            @if ($banner_t1)
            <a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_t1->main_image }}" alt="{{ $banner_t1->name }}">
            </a>
            @endif
            @if ($banner_t2)
            <a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_t2->main_image }}" alt="{{ $banner_t2->name }}">
            </a>
            @endif
        </div>
    </div>
</div>
</div>
<script>

    window.onload = function() {

        $.ajax({
            url: '/update_statistic',
            method: 'GET',
            data: {
                category: 'articles',
                article_id: {{ $id }},
            },
            success: function(json){
                console.log(json);
            }
        });

        gascrolldepth.init({
            elements: ['#statistic_calculation'],
            userTiming: false,
            gtmOverride: false,
            eventHandler: function(data) {

                if(data.eventAction === 'Percentage'){

                    $.ajax({
                        url: '/add_article_statistic',
                        method: 'GET',
                        data: {
                            url: '{{ Request::url() }}',
                            percent: data.eventLabel,
                            category: 'interview',
                            article_id: {{ $id }},
                        },
                        success: function(json){
                            console.log(json);
                        }
                    });


                }
            }
        })

    };
</script>

@include('partials.brands_carusel')

@endsection
