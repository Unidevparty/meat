@extends('layouts.main', [
'title' => $photogallery->title,
'description' => $photogallery->description,
'keywords' => $photogallery->keywords,
])

@section('page_content')
@php
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

<div class="content-row">
    <ul class="breadcrumbs">
        <li><a href="/">Главная</a></li>
        <li><a href="{{ route('photogallery.list') }}">Галерея</a></li>
        <li>{{ $photogallery->name }}</li>
    </ul>
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            <article class="article">
                <header class="article-header gallery-header">
                    <div class="article-header-in">
                        <h1>{{ $photogallery->name }}</h1>
                        <img src="{{ $photogallery->main_image }}" alt="{{ $photogallery->name }}" class="article-header-bg">
                        <span class="stats">
                            <span class="stats-unit">
								<i class="icon icon-camera"></i>
								{{ count($photogallery->photos) }}
							</span>
							<span class="stats-unit">
								<i class="icon icon-views"></i>
								{{ $photogallery->views or 0 }}
							</span>
							<span class="stats-unit">                                    
							@if ($photogallery->comments_count == 0)
										
							@else
								<i class="icon icon-comments"></i>
								{{ $photogallery->comments_count }}
							@endif
							</span>
                        </span>
                    </div>
                </header>

                {!! $photogallery->text !!}
            </article>
        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @include('partials.subscribe_side_form')

            <div class="side-box">
                @include('partials.more_news')
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
                @foreach ($photogallery->photos as $photo)
                <figure class="gallery-item">
                    <a data-fancybox="photogallery" href="{{ $photo->photo }}" data-caption="{{ strip_tags($photo->description) }}">
                        <img src="{{ resize($photo->photo, 940, 940, false) }}" alt="">
                    </a>
                    @if ($photo->description)
                    <figcaption>{!! $photo->description !!}</figcaption>
                    @endif

                </figure>
                @endforeach


                <footer class="article-footer">
                    <div class="author-module">
                        <div class="author-details">
                            {{ LocalizedCarbon::instance($photogallery->published_at)->formatLocalized('%d %f ‘%y') }}
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
                        {{ $photogallery->views }}
                    </div>

                </footer>
            </div>
            <div class="content-section">
                @include('partials.comments', ['page' => $photogallery, 'type' => 'photogallery'])
            </div>

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

           {{--  @if (!empty($banner_h1))
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

            <div class="mob-show mob-additional-banner">
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
                @if (!empty($banner_p1))
                <a href="{{ $banner_p1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
                </a>
                @endif
                @if (!empty($banner_t1))
                <a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
                </a>
                @endif
                @if (!empty($banner_t2))
                <a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
                </a>
                @endif
            </div>


        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @if (!empty($banner_k))
            <a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
                <img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}">
            </a>
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
