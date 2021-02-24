<div class="content-row banners-row x1b">
	<div class="content-cells">
		@php
			$banner_x1 = getBanner('X-1');
			$banner_x2 = getBanner('X-2');
		@endphp

		@if (!empty($banner_x1))
			<div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
				<a href="{{ $banner_x1->fake_url }}" target="_blank">
					<img src="{{ $banner_x1->main_image }}" alt="{{ $banner_x1->name }}">
				</a>
			</div>
		@endif
		@if (!empty($banner_x2))
			<div class="content-cell content-cell-6 content-cell-tab-12 mob-hide">
				<a href="{{ $banner_x2->fake_url }}" target="_blank">
					<img src="{{ $banner_x2->main_image }}" alt="{{ $banner_x2->name }}">
				</a>
			</div>
		@endif
		@if (!empty($banner_x1))
			<div class="mob-additional-banner nobefore">
				<a href="{{ $banner_x1->fake_url }}" target="_blank">
					<img src="{{ $banner_x1->mobile_image }}" alt="{{ $banner_x1->name }}">
				</a>
			</div>
		@endif
		@if (!empty($banner_x2))
			<div class="mob-additional-banner">
				<a href="{{ $banner_x2->fake_url }}" target="_blank">
					<img src="{{ $banner_x2->mobile_image }}" alt="{{ $banner_x2->name }}">
				</a>
			</div>
		@endif
	</div>
</div>
