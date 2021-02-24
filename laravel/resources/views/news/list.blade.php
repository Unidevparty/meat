@extends('layouts.main')

@section('page_content')

<div class="content-row">
    <div class="section-header">
        <div class="h">Новости</div>
        {{-- <ul class="tags-switch">
				<li><a href="{{ route('news.list') }}" class="{{ active(['news.list'], 'current') }}">Все</a></li>
        @foreach ($tags as $tag)
        <li><a href="{{ route('news.tag', $tag->alias) }}" class="{{ !empty($current_tag) && $tag->alias == $current_tag->alias ? 'current' : '' }}">{{ $tag->name }}</a></li>
        @endforeach
        </ul> --}}
    </div>
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            <div class="content-section">
                <div class="articles-thumbs">
                    <?php $news2 = $news->splice(9); ?>
                    @foreach ($news as $new)
                    @include('news.thumb', ['new' => $new])
                    @endforeach


                </div>
            </div>



        </div>
        <div class="content-cell content-cell-aside mob-hide">
            @include('partials.subscribe_side_form')

            @php
            $banner_news = getBanner('news');
            @endphp
            @if (!empty($banner_news))
            <a href="{{ $banner_news->fake_url }}" target="_blank" rel="nofollow" class="banner">
                <img src="{{ $banner_news->main_image }}" alt="{{ $banner_news->name }}">
            </a>
            @endif
        </div>
    </div>
</div>


<div class="content-row banners-row">
    <div class="content-cells">
        @php
        $banner_b1 = getBanner('B-1');
        $banner_b2 = getBanner('B-2');
        $banner_a3 = getBanner('A-3');
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
            <div class="content-section">
                <div class="articles-thumbs">
                    <?php $news3 = $news2->splice(9); ?>
                    @foreach ($news2 as $new)
                    @include('news.thumb', ['new' => $new])
                    @endforeach

                </div>
            </div>

            @php
            $banner_k = getBanner('K');
            @endphp
            @if (!empty($banner_k))
            <div class="mob-additional-banner">
                <a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
                </a>
            </div>
            @endif
            @php
            $banner_h1 = getBanner('H-1');
            $banner_a3 = getBanner('A-3');
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
            <div class="content-section">
                <div class="articles-thumbs articles-thumbs-all">

                    @foreach ($news3 as $new)
                    @include('news.thumb', ['new' => $new])
                    @endforeach

                    @if ($total > 39)
                    <a href="{{ route('news.more') }}?page=1&tag_alias={{ $tag_alias }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
                    @endif
                    <div class="mob-additional-banner load_more_before">
                        @php
                        $banner_c1 = getBanner('C-1');
                        $banner_c2 = getBanner('C-2');
                        $banner_p = getBanner('P-1');
                        @endphp
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
                        @if (!empty($banner_p))
                        <a href="{{ $banner_p->fake_url }}" target="_blank" rel="nofollow">
                            <img src="{{ $banner_p->mobile_image }}" alt="{{ $banner_p->name }}">
                        </a>
                        @endif
                    </div>
                    @php
                    $banner_t1 = getBanner('T-1');
                    $banner_t2 = getBanner('T-2');
                    @endphp

                    @if (!empty($banner_t1) || !empty($banner_t2))
                    <div class="mob-additional-banner">
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
                    @endif
                </div>
            </div>
        </div>
        <div class="content-cell content-cell-aside mob-hide">
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
