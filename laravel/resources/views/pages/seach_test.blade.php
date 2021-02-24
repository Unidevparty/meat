@extends('layouts.main')

@section('page_content')

	@php
		$banner_k  = getBanner('K');
		$banner_h1 = getBanner('H-1');
		$banner_c1 = getBanner('C-1');
		$banner_c2 = getBanner('C-2');
		$banner_p1 = getBanner('P-1');
		$banner_t1 = getBanner('T-1');
		$banner_t2 = getBanner('T-2');
	@endphp
			
	<div class="content-row">
		<div class="content-cells">
			<div class="content-cell content-cell-main">
				<article class="article">
					<div class="section-header">
						<h1 class="h">{{ $page->name }}</h1>
						@if (hasPermissions('pages'))
							<a href="{{ route('page.edit', $page->id) }}" class="edit_btn">Изменить</a>
						@endif
					</div>
					
					{!! $page->text !!}
				<div class="ya-site-form ya-site-form_inited_no" data-bem="{&quot;action&quot;:&quot;http://meat-expert.ru/seach_test&quot;,&quot;arrow&quot;:false,&quot;bg&quot;:&quot;transparent&quot;,&quot;fontsize&quot;:13,&quot;fg&quot;:&quot;#000000&quot;,&quot;language&quot;:&quot;ru&quot;,&quot;logo&quot;:&quot;rb&quot;,&quot;publicname&quot;:&quot;Поиск по meat-expert.ru&quot;,&quot;suggest&quot;:true,&quot;target&quot;:&quot;_self&quot;,&quot;tld&quot;:&quot;ru&quot;,&quot;type&quot;:2,&quot;usebigdictionary&quot;:false,&quot;searchid&quot;:2442429,&quot;input_fg&quot;:&quot;#000000&quot;,&quot;input_bg&quot;:&quot;#ffffff&quot;,&quot;input_fontStyle&quot;:&quot;normal&quot;,&quot;input_fontWeight&quot;:&quot;normal&quot;,&quot;input_placeholder&quot;:&quot;Искать на мясном эксперте&quot;,&quot;input_placeholderColor&quot;:&quot;#cccccc&quot;,&quot;input_borderColor&quot;:&quot;#7f9db9&quot;}">
				
				<form action="https://yandex.ru/search/site/" method="get" target="_self" accept-charset="utf-8"><input type="hidden" name="searchid" value="2442429"/><input type="hidden" name="l10n" value="ru"/>
				
				<input type="hidden" name="reqenc" value=""/><input type="search" name="text" value=""/><input type="submit" value="поМЯСить"/>
				</form>
				
				</div>
				
				<style type="text/css">.ya-page_js_yes .ya-site-form_inited_no { display: none; }</style>
				
				<script type="text/javascript">(function(w,d,c){var s=d.createElement('script'),h=d.getElementsByTagName('script')[0],e=d.documentElement;if((' '+e.className+' ').indexOf(' ya-page_js_yes ')===-1){e.className+=' ya-page_js_yes';}s.type='text/javascript';s.async=true;s.charset='utf-8';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//site.yandex.net/v2.0/js/all.js';h.parentNode.insertBefore(s,h);(w[c]||(w[c]=[])).push(function(){Ya.Site.Form.init()})})(window,document,'yandex_site_callbacks');</script>
				
				<div id="ya-site-results" data-bem="{&quot;tld&quot;: &quot;ru&quot;,&quot;language&quot;: &quot;ru&quot;,&quot;encoding&quot;: &quot;&quot;,&quot;htmlcss&quot;: &quot;1.x&quot;,&quot;updatehash&quot;: true}"></div><script type="text/javascript">(function(w,d,c){var s=d.createElement('script'),h=d.getElementsByTagName('script')[0];s.type='text/javascript';s.async=true;s.charset='utf-8';s.src=(d.location.protocol==='https:'?'https:':'http:')+'//site.yandex.net/v2.0/js/all.js';h.parentNode.insertBefore(s,h);(w[c]||(w[c]=[])).push(function(){Ya.Site.Results.init();})})(window,document,'yandex_site_callbacks');</script>

<style>
#ya-site-results
{
    color: #cc0000;
    background: #f3f3f3;
}

#ya-site-results .b-pager__current,
#ya-site-results .b-serp-item__number
{
    color: #cc0000 !important;
}

#ya-site-results
{
    font-family: Arial !important;
}

#ya-site-results :visited,
#ya-site-results .b-pager :visited,
#ya-site-results .b-foot__link:visited,
#ya-site-results .b-copyright__link:visited
{
    color: #999999;
}

#ya-site-results a:link,
#ya-site-results a:active,
#ya-site-results .b-pseudo-link,
#ya-site-results .b-head-tabs__link,
#ya-site-results .b-head-tabs__link:link,
#ya-site-results .b-head-tabs__link:visited,
#ya-site-results .b-dropdown__list .b-pseudo-link,
#ya-site-results .b-dropdowna__switcher .b-pseudo-link,
.b-popupa .b-popupa__content .b-menu__item,
#ya-site-results .b-foot__link:link,
#ya-site-results .b-copyright__link:link,
#ya-site-results .b-serp-item__mime,
#ya-site-results .b-pager :link
{
    color: #000000;
}

#ya-site-results .b-head__l
{
	display: none;
	
}


#ya-site-results :link:hover,
#ya-site-results :visited:hover,
#ya-site-results .b-pseudo-link
{
    color: #FF0000 !important;
}

#ya-site-results .l-page,
#ya-site-results .b-bottom-wizard
{
    font-size: 13px;
}

#ya-site-results .b-pager
{
    font-size: 1.25em;
}

#ya-site-results .b-serp-item__text,
#ya-site-results .ad
{
    font-style: normal;
    font-weight: normal;
}

#ya-site-results .b-serp-item__title-link,
#ya-site-results .ad .ad-link
{
    font-style: normal;
    font-weight: bold;
}

#ya-site-results .ad .ad-link a
{
    font-weight: bold;
}

#ya-site-results .b-serp-item__title,
#ya-site-results .ad .ad-link
{
    font-size: 16px;
}

#ya-site-results .b-serp-item__title-link:link,
#ya-site-results .b-serp-item__title-link
{
    font-size: 1em;
}

#ya-site-results .b-serp-item__number
{
    font-size: 13px;
}

#ya-site-results .ad .ad-link a
{
    font-size: 0.88em;
}

#ya-site-results .b-serp-url,
#ya-site-results .b-direct .url,
#ya-site-results .b-direct .url a:link,
#ya-site-results .b-direct .url a:visited
{
    font-size: 13px;
    font-style: normal;
    font-weight: normal;
    color: #cc0000;
}

#ya-site-results .b-serp-item__links-link
{
    font-size: 13px;
    font-style: normal;
    font-weight: normal;
    color: #000000 !important;
}

#ya-site-results .b-pager__inactive,
#ya-site-results .b-serp-item__from,
#ya-site-results .b-direct__head-link,
#ya-site-results .b-image__title,
#ya-site-results .b-video__title
{
    color: #000000 !important;
}

#ya-site-results .b-pager__current,
#ya-site-results .b-pager__select
{
    background: #E0E0E0;
}

#ya-site-results .b-foot,
#ya-site-results .b-line
{
    border-top-color: #E0E0E0;
}

#ya-site-results .b-dropdown__popup .b-dropdown__list,
.b-popupa .b-popupa__content
{
    background-color: #f3f3f3;
}

.b-popupa .b-popupa__tail
{
    border-color: #E0E0E0 transparent;
}

.b-popupa .b-popupa__tail-i
{
    border-color: #f3f3f3 transparent;
}

.b-popupa_direction_left.b-popupa_theme_ffffff .b-popupa__tail-i,
.b-popupa_direction_right.b-popupa_theme_ffffff .b-popupa__tail-i
{
    border-color: transparent #f3f3f3;
}

#ya-site-results .b-dropdowna__popup .b-menu_preset_vmenu .b-menu__separator
{
    border-color: #E0E0E0;
}

.b-specification-list,
.b-specification-list .b-pseudo-link,
.b-specification-item__content label,
.b-specification-item__content .b-link,
.b-specification-list .b-specification-list__reset .b-link
{
    color: #cc0000 !important;
    font-family: Arial;
    font-size: 13px;
    font-style: normal;
    font-weight: normal;
}

.b-specification-item__content .b-calendar__title
{
    font-family: Arial;
    color: #cc0000;
    font-size: 13px;
    font-style: normal;
    font-weight: normal;
}

.b-specification-item__content .b-calendar-month__day_now_yes
{
    color: #E0E0E0;
}

.b-specification-item__content .b-calendar .b-pseudo-link
{
    color: #cc0000;
}

.b-specification-item__content
{
    font-family: Arial !important;
    font-size: 13px;
}

.b-specification-item__content :visited
{
    color: #999999;
}

.b-specification-item__content .b-pseudo-link:hover,
.b-specification-item__content :visited:hover
{
    color: #FF0000 !important;
}

#ya-site-results .b-popupa .b-popupa__tail-i
{
    background: #f3f3f3;
    border-color: #E0E0E0 !important;
}
</style>



				
					<div class="mob-additional-banner">
						<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_k->mobile_image }}" alt="{{ $banner_k->name }}">
						</a>
					</div>
					
					<div class="content-section banner lap-hide">
						<a href="{{ $banner_a3->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_a3->mobile_image }}" alt="{{ $banner_a3->name }}">
						</a>
					</div>
					

					<div class="mob-additional-banner">
						<a href="{{ $banner_h1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_h1->mobile_image }}" alt="{{ $banner_a3->name }}">
						</a>
					</div>


					
					<div class="mob-additional-banner">
						<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_c1->mobile_image }}" alt="{{ $banner_c1->name }}">
						</a>
						<a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_c2->mobile_image }}" alt="{{ $banner_c2->name }}">
						</a>
						<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_p1->mobile_image }}" alt="{{ $banner_p1->name }}">
						</a>
						<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_t1->mobile_image }}" alt="{{ $banner_t1->name }}">
						</a>
						<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
							<img src="{{ $banner_t2->mobile_image }}" alt="{{ $banner_t2->name }}">
						</a>
					</div>

					
				</article>
			</div>

			<div class="content-cell content-cell-aside mob-hide">
				<a href="{{ $banner_k->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_k->main_image }}" alt="{{ $banner_k->name }}">
				</a>
				<a href="{{ $banner_c1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_c1->main_image }}" alt="{{ $banner_c1->name }}">
				</a>
				<a href="{{ $banner_c2->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_c2->main_image }}" alt="{{ $banner_c2->name }}">
				</a>
				<a href="{{ $banner_p1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_p1->main_image }}" alt="{{ $banner_p1->name }}">
				</a>
				<a href="{{ $banner_t1->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_t1->main_image }}" alt="{{ $banner_t1->name }}">
				</a>
				<a href="{{ $banner_t2->fake_url }}" class="banner" target="_blank" rel="nofollow">
					<img src="{{ $banner_t2->main_image }}" alt="{{ $banner_t2->name }}">
				</a>
			</div>
		</div>
	</div>


	@include('partials.brands_carusel')
@endsection