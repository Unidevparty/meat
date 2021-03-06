@extends('layouts.main')

@section('page_content')

<div class="content-row">
    <div class="section-header">
        <div class="h">Статьи</div>
        <!--<ul class="tags-switch">
				<li><a href="{{ route('articles.list') }}" class="{{ active(['articles.list'], 'current') }}">Все</a></li>
				@foreach ($tags as $tag)
					<li><a href="{{ route('articles.tag', $tag->alias) }}" class="{{ !empty($current_tag) && $tag->alias == $current_tag->alias ? 'current' : '' }}">{{ $tag->name }}</a></li>
				@endforeach
			</ul>-->
    </div>
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            <div class="content-section">
                <div class="filter-block">
                    <form action="" method="get" class="filter">
                        {{-- <div class="filter-cell">
								<select class="custom-select">
									<option value="">дата</option>
									<option value="">дата</option>
								</select>
							</div> --}}
                        <div class="filter-cell">
                            <select class="custom-select" name="author">
                                <option value="">Автор не указан</option>
                                @foreach ($authors as $id => $author)
                                @if ($author)
                                <option value="{{ $id }}" {{ $selected_author == $id ? 'selected' : '' }}>{{ $author }}</option>
                                @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="filter-cell">
                            <select class="custom-select" name="company">
                                <option value="">Компания не указана</option>
                                @foreach ($companies as $company)
                                <option value="{{ $company->id }}" {{ $selected_company == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="filter-cell">
                            <div class="search-cmp">
                                <input type="text" value="{{ $search }}" name="search" placeholder="Поиск по разделу">
                                <button>
                                    <i class="icon icon-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="content-section">
                <div class="articles-thumbs">

                    <?php $articles2 = $articles->splice(6); ?>
                    @foreach ($articles as $article)
                    @include('articles.thumb', ['article' => $article])
                    @endforeach



                </div>
            </div>



        </div>
        <div class="content-cell content-cell-aside  mob-hide">
            @include('partials.subscribe_side_form')

            @php
            $banner_articles = getBanner('articles');
            @endphp
            @if (!empty($banner_articles))
            <a href="{{ $banner_articles->fake_url }}" target="_blank" rel="nofollow" class="banner banner_articles">
                <img src="{{ $banner_articles->main_image }}" alt="{{ $banner_articles->name }}">
            </a>
            @endif
        </div>
    </div>
</div>


<div class="content-row banners-row">
    <div class="content-cells">
        @php
        $banner_a3 = getBanner('A-3');
        $banner_b1 = getBanner('B-1');
        $banner_b2 = getBanner('B-2');
        @endphp
        @if (!empty($banner_b1))
        <div class="content-cell content-cell-6 content-cell-tab-12 mob-hide banner_b1">
            <a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b1->main_image }}" alt="{{ $banner_b1->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b2))
        <div class="content-cell content-cell-6 content-cell-tab-12 mob-hide banner_b2">
            <a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b2->main_image }}" alt="{{ $banner_b2->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b1))
        <div class="mob-additional-banner nobefore banner_b1_m">
            <a href="{{ $banner_b1->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b1->mobile_image }}" alt="{{ $banner_b1->name }}">
            </a>
        </div>
        @endif
        @if (!empty($banner_b2))
        <div class="mob-additional-banner nobefore banner_b2_m">
            <a href="{{ $banner_b2->fake_url }}" target="_blank" rel="nofollow">
                <img src="{{ $banner_b2->mobile_image }}" alt="{{ $banner_b2->name }}">
            </a>
        </div>
        @endif
    </div>
</div>
@if (!empty($banner_a3))
<div class="content-row banner-through tab-gea banner_a3_t">
    <a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
        <img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->name }}">
    </a>
</div>
@endif


@php
$banner_k = getBanner('K');
$banner_c1 = getBanner('C-1');
$banner_c2 = getBanner('C-2');
$banner_p1 = getBanner('P-1');
$banner_t1 = getBanner('T-1');
$banner_t2 = getBanner('T-2');
$banner_h1 = getBanner('H-1');
@endphp

<div class="content-row">
    <div class="content-cells">
        <div class="content-cell content-cell-main">
            <div class="content-section">
                <div class="articles-thumbs">
                    <?php $articles3 = $articles2->splice(9); ?>
                    @foreach ($articles2 as $article)
                    @include('articles.thumb', ['article' => $article])
                    @endforeach
                </div>
            </div>
            @php
            $banner_k = getBanner('K');
            @endphp
            @if (!empty($banner_k))
            <div class="mob-additional-banner banner_k_m">
                <a href="{{ $banner_k->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
                </a>
            </div>
            @endif
            @if (!empty($banner_h1))
            <div class="content-section banner mob-hide banner_h1">
                <a href="{{ $banner_h1->fake_url }}" target="_blank" rel="nofollow">
                    <img src="{{ $banner_h1->main_image }}" alt="{{ $banner_h1->name }}">
                </a>
            </div>
            @endif
            @if (!empty($banner_h1))
            <div class="mob-additional-banner nobefore banner_h1_m">
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

            {{-- @if (!empty($banner_c1))
					<div class="content-section banner lap-hide banner_c1_m">
						<a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
            <img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
            </a>
        </div>
        @endif --}}
		<br/>
        <div class="content-section">
            <div class="articles-thumbs articles-thumbs-all">

                @foreach ($articles3 as $article)
                @include('articles.thumb', ['article' => $article])
                @endforeach


                @if ($total > 27)
                <a href="{{ route('articles.more') }}?page=1&author={{ $selected_author }}&company={{ $selected_company }}&search={{ $search }}&tag_alias={{ $tag_alias }}" class="btn load_more btn-reg btn-red wide">загрузить еще</a>
                @endif


                @if (!empty($banner_c1))
                <div class="mob-additional-banner load_more_before banner_c2_m">
                    <a href="{{ $banner_c1->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
                    </a>
                </div>
                @endif
                @if (!empty($banner_c2))
                <div class="mob-additional-banner banner_c2_m">
                    <a href="{{ $banner_c2->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
                    </a>
                </div>
                @endif
                @if (!empty($banner_p1))
                <div class="mob-additional-banner banner_p1_m">
                    <a href="{{ $banner_p1->fake_url }}" target="_blank" rel="nofollow">
                        <img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
                    </a>
                </div>
                @endif
            </div>
        </div>

        <div class="wbox wbox-pd">
            <div class="section-h">
                <div class="h">Читаемое</div>
            </div>
            <div class="articles-thumbs top-stories">
                @foreach ($most_viewed as $article)
                @if ($loop->index < 2) @include('articles.thumb', ['article'=> $article, 'size' => 'big'])
                    @else
                    @include('articles.thumb', ['article' => $article])
                    @endif
                    @endforeach
            </div>
        </div>

    </div>
    <div class="content-cell content-cell-aside mob-hide">
        @if (!empty($banner_k))
         <a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
				<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}"></a>
        @endif
        @if (!empty($banner_c1))
        <a href="{{ $banner_c1->fake_url }}" class="banner banner_c1" target="_blank" rel="nofollow">
            <img src="{{ $banner_c1->main_image }}" alt="{{ $banner_c1->name }}">
        </a>
        @endif
        @if (!empty($banner_c2))
        <a href="{{ $banner_c2->fake_url }}" class="banner banner_c2" target="_blank" rel="nofollow">
            <img src="{{ $banner_c2->main_image }}" alt="{{ $banner_c2->name }}">
        </a>
        @endif
        @if (!empty($banner_p1))
        <a href="{{ $banner_p1->fake_url }}" class="banner banner_p1" target="_blank" rel="nofollow">
            <img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}">
        </a>
        @endif
        @if (!empty($banner_t1))
        <a href="{{ $banner_t1->fake_url }}" class="banner banner_t1" target="_blank" rel="nofollow">
            <img src="{{ $banner_t1->main_image }}" alt="{{ $banner_t1->name }}">
        </a>
        @endif
        @if (!empty($banner_t2))
        <a href="{{ $banner_t2->fake_url }}" class="banner banner_t2" target="_blank" rel="nofollow">
            <img src="{{ $banner_t2->main_image }}" alt="{{ $banner_t2->name }}">
        </a>
        @endif
    </div>

</div>
@if (!empty($banner_t1) || !empty($banner_t2))
<div class="mob-additional-banner">
    @if (!empty($banner_t1))
    <a href="{{ $banner_t1->fake_url }}" target="_blank" rel="nofollow" class="banner_t1">
        <img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
    </a>
    @endif
    @if (!empty($banner_t2))
    <a href="{{ $banner_t2->fake_url }}" target="_blank" rel="nofollow" class="banner_t2">
        <img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
    </a>
    @endif
</div>
@endif
</div>


@include('partials.brands_carusel')

@endsection

@section('scripts')
<script type="text/javascript">
    $('.filter-block select').on('selectmenuchange', function() {
        var form = $(this).closest('form');

        $('select', form).not(this).each(function() {
            $(this).val('');
        });

        $('[name="search"]', form).each(function() {
            $(this).val('');
        });

        form.submit();
    });

</script>
@endsection
