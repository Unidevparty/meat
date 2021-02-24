<div class="interview-card company-card vacancy-card {{ $job->isfixed ? 'vacancy-card-hl' : '' }}">
	<figure>
		@if ($job->company->logo)
			<img src="{{ resize($job->company->logo, 86, 86, false) }}" alt="{{ $job->company->name }}">
		@endif
	</figure>

	<div class="interview-card-description">
		<div class="h">
			<span class="date">
				@if ($job->isfixed)
					Рекомендовано «Мясным Экспертом»
				@else
					{{ $job->published_at ? LocalizedCarbon::instance($job->published_at)->formatLocalized('%d&nbsp;%f&nbsp;‘%y') : '' }}
				@endif
			</span>
			<span class="author">
				@if (!$job->our)
					<a href="{{ route('job.show', $job->alias) }}">{{ $job->company->name }}</a>
				@endif
				<span class="type">{{ $job->company_type->name }}</span>
			</span>
			<span class="stats">
				<span class="stats-unit">
					<i class="icon icon-views"></i>
					{{ $job->views or 0 }}
				</span>
			</span>
		</div>
		<h3><a href="{{ route('job.show', $job->alias) }}">{{ $job->name }}</a></h3>
		<footer>
			<div class="vac-stats">
				@if ($job->city)
					<span class="vac-stats-unit"><i class="icon icon-geo"></i> {{ $job->city }}</span>
				@endif
				{{-- <span class="vac-stats-unit"><i class="icon icon-role"></i> {{ $job->city }}</span> --}}
				<span class="vac-stats-unit"><i class="icon icon-income"></i> <b>{{ number_format($job->zarplata, 0, ',', ' ') }}<span class="rub">i</span></b> {{ $job->zp_options }}.</span>
			</div>
		</footer>
	</div>
</div>
