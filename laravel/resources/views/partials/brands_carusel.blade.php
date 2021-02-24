<hr class="hr">

<div class="content-row">
	<div class="brands-carusel">
		<ul class="brands-carusel-proper">
			@foreach (getPartnersBanners() as $banner)
				<li>
					<a href="{{ $banner->fake_url }}" target="_blank">
						<img src="{{ $banner->main_image }}" alt="{{ $banner->name }}">
					</a>
				</li>
			@endforeach
		</ul>
	</div>
</div>

<hr class="hr">