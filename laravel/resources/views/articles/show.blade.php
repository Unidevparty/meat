@extends('layouts.main')

@section('page_content')
    @php
        $banner_news = getBanner('news');
        $banner_a3 = getBanner('A-3');
        $banner_articles = getBanner('articles');

        $banner_k = getBanner('K');
        $banner_c1 = getBanner('C-1');
        $banner_c2 = getBanner('C-2');
        $banner_p1 = getBanner('P-1');
        $banner_t1 = getBanner('T-1');
        $banner_t2 = getBanner('T-2');
    @endphp

    @php
        $id = $article->id;
    @endphp
<div itemscope itemtype="https://schema.org/Article">
    <div class="content-row" id="scrolldepth">
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
        @if (hasPermissions('articles'))
            <a href="{{ route('article.edit', $article->id) }}" class="edit_btn">Изменить</a>
        @endif

<!--         <ul class="breadcrumbs">
            <li><a href="/">Главная</a></li>
            <li><a href="{{ route('articles.list') }}">Статьи</a></li>
            <li>{{ $article->name }}</li>
        </ul> -->
        <ul itemscope itemtype="https://schema.org/BreadcrumbList" class="breadcrumbs">
            <li itemprop="itemListElement" itemscope
          itemtype="https://schema.org/ListItem"><a itemprop="item" href="/"><span itemprop="name">Главная</span></a><meta itemprop="position" content="1" /></li>
            <li itemprop="itemListElement" itemscope
          itemtype="https://schema.org/ListItem"><a itemprop="item" href="{{ route('articles.list') }}"><span itemprop="name">Статьи</span></a><meta itemprop="position" content="2" /></li>
            <li itemprop="itemListElement" itemscope
          itemtype="https://schema.org/ListItem"><span itemprop="name">{{ $article->name }}</span><meta itemprop="position" content="3" /></li>
        </ul>
        <header class="article-header">
            <div class="article-header-in">
                <h1 itemprop="headline name">{{ $article->name }}</h1>
                <meta itemprop="description" content="{{ $article->name }}">
                <div class="tags">
                    @foreach ($article->tags as $tag)
                        <small class="tag"><a href="{{ route('articles.tag', $tag->alias) }}">{{ $tag->name }}</a></small>
                    @endforeach
                </div>
                <link itemprop="image" href="{{ $article->main_image }}">
                <img src="{{ $article->main_image }}" alt="{{ $article->name }}" class="article-header-bg">
            </div>
        </header>
        <div class="content-cells" id="statistic_calculation">
            <div class="content-cell content-cell-main">
                <div class="article">

                    @if ($article->authors()->count())
                        <div class="authors_in_article">
                            @foreach ($article->authors as $author)
                                <div class="author">
                                    <div class="author-in">
                                        <img src="{{ resize($author->photo, 91, 91) }}" alt="{{ $author->name }}" width="65">
                                        <div class="data">
                                            <div class="name" itemprop="author">{{ $author->name }}</div>
                                            <div class="company">{{ $author->company->name }}</div>
                                            <div class="post">{{ $author->post }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <?php
                    $text_parts = slit_text($article->text, 3);
                    ?>
                    <?php
                    $text_parts2 = slit_text($text_parts[0], 0, 50);
                    ?>
                    {!! $text_parts2[0] !!}
                    @if ($banner_articles)
                        <div class="mob-additional-banner">
                            <a href="{{ $banner_articles->fake_url }}" target="_blank" rel="nofollow">
                                <img src="{{ $banner_articles->mobile_image }}" alt="{{ $banner_articles->name }}">
                            </a>
                        </div>
                    @endif
                    {!! $text_parts2[1] !!}
                </div>
            </div>
            <div class="content-cell content-cell-aside mob-hide">
                <a href="{{ $banner_articles->fake_url }}" target="_blank" rel="nofollow" class="banner">
                    <img src="{{ $banner_articles->main_image }}" alt="{{ $banner_articles->name }}">
                </a>
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
                <div class="article">
                	<div itemprop="articleBody">
                		{!! $text_parts[1] !!}	
                	</div>
                    @if (!member() && $article->for_registered)
                        <div class="blockmessage">
                            <div class="in">
                                <div class="icn-placeholder">
                                    <i class="icon icon-blocked"></i>
                                </div>
                                <div class="msg">Полная версия статьи доступна только зарегистрированным пользователям</div>
                                <a href="" class="btn btn-reg btn-header-personal" data-pop-link="login">Вход <span class="mob-hide">/ регистрация</span></a>
                            </div>
                        </div>
                    @endif

                    

                    <footer class="article-footer">

                        @if ($article->authors()->count())
                            @foreach ($article->authors as $author)
                                <div class="author-module">
                                    <div class="author-userpic">
                                        <img src="{{ resize($author->photo, 44, 44) }}" alt="{{ $author->name }}" width="44">
                                    </div>
                                    <div class="author-details">
                                        <span class="author-name">{{ $author->name }}</span>
                                        <span class="author-date">{{ LocalizedCarbon::instance($article->published_at)->formatLocalized('%d %f ‘%y') }}</span>
                                        <meta itemprop="datePublished" content="{!! $article->published_at->format('Y-m-d') !!}">
                                        <meta itemprop="dateModified" content="{!! $article->published_at->format('Y-m-d') !!}">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <meta itemprop="author" content="Мясной Эксперт">
                            <meta itemprop="datePublished" content="2020-10-10">
                            <meta itemprop="dateModified" content="2020-10-10">
                        @endif

                        <div class="share">
                            <div class="label">Поделиться:</div>
                            <div class="share-cnt">
                                @include('partials.share')
                            </div>
                        </div>
                        <div class="views">
                            <i class="icon icon-views"></i>
                            {{ $article->views or 0 }}
                        </div>
						@if (!empty($banner_k))
                        <div class="mob-additional-banner">
                            <a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
                                <img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
                            </a>
                        </div>
                    @endif	
                    </footer>
                </div>
                <div class="content-section">
                    <div class="neighbours-navi">
                        @if ($prev = $article->previous)
                            <a href="{{ route('articles.show', $prev->alias) }}" class="neighbour-link go-prev">
                                <small>Предыдущая статья</small>
                                <span class="h">{{ $prev->name }}</span>
                                <i class="icon icon-prev"></i>
                            </a>
                        @endif

                        @if ($next = $article->next)
                            <a href="{{ route('articles.show', $next->alias) }}" class="neighbour-link go-next">
                                <small>Следующая статья</small>
                                <span class="h">{{ $next->name }}</span>
                                <i class="icon icon-next"></i>
                            </a>
                        @endif

                    </div>
                </div>

                @include('partials.comments', ['page' => $article, 'type' => 'articles'])

                <div id="hypercomments_widget"></div>
                <script type="text/javascript">
                    _hcwp = window._hcwp || [];
                    _hcwp.push({
                        widget: "Stream",
                        widget_id: 106049,
                        social: "facebook, twitter, vk, odnoklassniki"
                    });
                    (function() {
                        if ("HC_LOAD_INIT" in window) return;
                        HC_LOAD_INIT = true;
                        var lang = (navigator.language || navigator.systemLanguage || navigator.userLanguage || "en").substr(0, 2).toLowerCase();
                        var hcc = document.createElement("script");
                        hcc.type = "text/javascript";
                        hcc.async = true;
                        hcc.src = ("https:" == document.location.protocol ? "https" : "http") + "://w.hypercomments.com/widget/hc/106049/" + lang + "/widget.js";
                        var s = document.getElementsByTagName("script")[0];
                        s.parentNode.insertBefore(hcc, s.nextSibling);
                    })();

                </script>

                @php
                    $banner_h1 = getBanner('H-1');
                @endphp
                @if (!empty($banner_h1))
                    <div class="content-section banner mob-hide">
                        <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                            <img src="{{ $banner_h1->main_image }}" alt="{{ $banner_h1->name }}">
                        </a>
                    </div>
                @endif

                @if (!empty($banner_h1))
                    <div class="mob-additional-banner nobefore">
                        <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                            <img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_h1->name }}">
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
                        @if (!empty($banner_c1) || !empty($banner_c2))
                            <div class="mob-additional-banner">
                                @if (!empty($banner_c1))
                                    <a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
                                        <img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
                                    </a>
                                @endif
                                @if (!empty($banner_c2))
                                    <a href="{{ $banner_c2->fake_url }}" target="_blank" rel="nofollow">
                                        <img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
                                    </a>
                                @endif
                            </div>
                        @endif
                        @if ($banner_p1)
                            <div class="mob-additional-banner">
                                <a href="{{ $banner_p1->fake_url }}" target="_blank" rel="nofollow">
                                    <img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
                                </a>
                            </div>
                        @endif
                    </div>
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
					<img src="https://meat-expert.ru/ads/prizma/1.jpg" alt="аромадон">
				</a>
			</div>
			@endif --}}
				<!--noindex-->
                @include('blog.most_viewed')
				<!--/noindex-->
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
                @include('partials.subscribe_side_form')
				<!--noindex-->
                <div class="side-box">
                    @include('partials.more_news')
                </div>
				<!--/noindex-->
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
</div>
    <script>
        //  window.onload = function() {
        //     jQuery.scrollDepth({
        //         elements: ['#scrolldepth'],
        //         eventHandler: function(data) {
        //             console.log(data);
        //         }
        //     });

        // };
        // window.onload = function() {
        //     jQuery(document).ready(function($) {
        //         $(window).scroll(function(){
        //             let percentArticle = Math.round(($(window).scrollTop() - $('header.header').height())/$('#scrolldepth').height()*100);
        //             if (percentArticle > 0 && percentArticle < 100) console.log(percentArticle)
        //         });
        //     })
        // };
    </script>

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
                                category: 'articles',
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
