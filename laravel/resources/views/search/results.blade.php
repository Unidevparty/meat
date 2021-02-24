@extends('layouts.main')

@section('page_content')

    @php
        $banner_a3 = getBanner('A-3');
        $banner_b1 = getBanner('B-1');
        $banner_b2 = getBanner('B-2');
    	$banner_k  = getBanner('K');
    	$banner_h1 = getBanner('H-1');
    	$banner_c1 = getBanner('C-1');
    	$banner_c2 = getBanner('C-2');
    	$banner_p1 = getBanner('P-1');
    	$banner_t1 = getBanner('T-1');
    	$banner_t2 = getBanner('T-2');
    @endphp


    <div class="content-row search-page">
        <div class="content-cells">
            <div class="content-cell content-cell-main">

                <div class="content-section">
                    <form method="get" action="{{ route('search') }}">

                        <div class="search-page-searchform">
                            <div class="form-search-in">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск...">
                                <button type="submit">
                                    <i class="icon icon-search"></i>
                                    <span>Искать</span>
                                </button>
                            </div>
                        </div>

                        <div class="section-header">
                            <h1 class="h">Результаты поиска ({{ $results->total() }})</h1>
                        </div>
                        <div class="search_query">по запросу: <b>{{ request('search') }}</b></div>
                        <div class="search-controls">
                            <a href="search-filter" class="filter-toggle">Фильтр Поиска</a>
                            <div class="search-sort">
                                <select class="custom-select" name="search_order">
                                    <option value="">По умолчанию</option>
                                    <option value="published_at-asc" {{ $order_select_value == 'published_at-asc' ? 'selected' : '' }}>По дате &uarr;</option>
                                    <option value="published_at-desc" {{ $order_select_value == 'published_at-desc' ? 'selected' : '' }}>По дате &darr;</option>
                                    <option value="views-asc" {{ $order_select_value == 'views-asc' ? 'selected' : '' }}>По популярности &uarr;</option>
                                    <option value="views-desc" {{ $order_select_value == 'views-desc' ? 'selected' : '' }}>По популярности &darr;</option>
                                </select>
                            </div>
                        </div>

                        <div class="filter-block filter-block-slim" id="search-filter">
                            <div class="advanced_search_form">
                                <input type="hidden" name="order_by" value="{{ request('order_by') }}">
                                <input type="hidden" name="order_dir" value="{{ request('order_dir') }}">

                                <ul class="filter-checks">
                                    <li><label><input type="checkbox" name="where[]" value="everywhere" {{ in_array('everywhere', request('where')) || !request('where') ? 'checked' : '' }}><span>Везде</span></label></li>
                                    <li><label><input type="checkbox" name="where[]" value="news" {{ in_array('news', request('where')) || !request('where') ? 'checked' : '' }}><span>Новости</span></label></li>
                                    <li><label><input type="checkbox" name="where[]" value="article" {{ in_array('article', request('where')) || !request('where') ? 'checked' : '' }}><span>Статьи</span></label></li>
                                    <li><label><input type="checkbox" name="where[]" value="interview" {{ in_array('interview', request('where')) || !request('where') ? 'checked' : '' }}><span>Интервью</span></label></li>
                                    <li><label><input type="checkbox" name="where[]" value="event" {{ in_array('event', request('where')) || !request('where') ? 'checked' : '' }}><span>События</span></label></li>
                                    <li><label><input type="checkbox" name="where[]" value="company" {{ in_array('company', request('where')) || !request('where') ? 'checked' : '' }}><span>Компании</span></label></li>
                                    <li><label><input type="checkbox" name="where" value="" disabled><span>Блоги</span></label></li>
                                    <li><label><input type="checkbox" name="where" value="" disabled><span>Форум</span></label></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="content-cell content-cell-aside mob-hide">
                @include('partials.subscribe_side_form')
            </div>
        </div>
    </div>





    {{-- @if (!empty($banner_a3))
		<div class="content-row banner-through tab-gea">
			<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
				<img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->position }}">
			</a>
		</div>
	@endif --}}



    <div class="content-row">
        <div class="content-cells">
            <div class="content-cell content-cell-main">
                <div class="content-section">

                    @if (!$results->count())
                        <h1>По вашему запросу ничего не найдено!</h1>
                    @endif


                    @foreach ($results as $result)
                        @include('search.card', ['result' => $result])
                    @endforeach

                    @if ($results->nextPageUrl())
                        <a href="{{ $results->nextPageUrl() }}" class="btn btn-reg btn-red wide load_more">загрузить еще</a>
                    @endif

                    <div class="load_more_before"></div>
                </div>
            </div>

        </div>
    </div>

    @if (!empty($banner_a3))
		<div class="content-row banner-through tab-gea show_on_mob">
			<a href="{{ $banner_a3->fake_url }}" target="_blank" rel="nofollow">
				<img src="{{ $banner_a3->tablet_image }}" alt="{{ $banner_a3->position }}">
			</a>
		</div>
	@endif

	@include('partials.brands_carusel')
@endsection


@section('scripts')
    <script>
        var advanced_search_form = $('.advanced_search_form').closest('form').eq(0);
        var timer;

        $('[type="checkbox"]', advanced_search_form).change(function() {
            //if (timer) clearTimeout(timer);

            var checkboxes = $('[type="checkbox"]', advanced_search_form).not(':disabled');

            $('#search-filter').removeClass('hlerror');

            if ($(this).val() == 'everywhere') {
                var checked = $(this).prop('checked');
                checkboxes.prop('checked', 1);

                if (!checked) checkboxes.prop('checked', 0);
            } else {
                var all_checked = true;

                for (var i = 0; i < checkboxes.length; i++) {
                    if (!checkboxes.eq(i).prop('checked') && checkboxes.eq(i).val() != 'everywhere') {
                        all_checked = false;
                        break;
                    }
                }


                $('[value="everywhere"]', advanced_search_form).prop('checked', all_checked);

            }

            timer = setTimeout(function() {
                //advanced_search_form.submit();
            }, 500);
        });

        $('[name="search_order"]').on('selectmenuchange', function() {
            var val = $(this).val().split('-');

            if (val.length == 2) {
                var order_by  = val[0];
                var order_dir = val[1];

                $('[name="order_by"]', advanced_search_form).val(order_by);
                $('[name="order_dir"]', advanced_search_form).val(order_dir);
            } else {
                $('[name="order_by"]', advanced_search_form).val('');
                $('[name="order_dir"]', advanced_search_form).val('');
            }

            advanced_search_form.submit();
        });

        $('.search-page .content-section form').submit(function(e){
            e.preventDefault();
            if(!$('#search-filter [type="checkbox"]:checked').length){
                $('#search-filter').addClass('hlerror');
            } else {
                $('.search-page .content-section form')[0].submit();
            }
        })

    </script>
@endsection
