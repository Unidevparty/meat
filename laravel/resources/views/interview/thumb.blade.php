<a href="{{ route('interviews.show', $interview->alias) }}" class="interview-card">
	<figure><img src="{{ $interview->preview }}" alt="{{ $interview->fio }}" width="184"></figure>
	<div class="interview-card-description">
		<h3>{{ $interview->fio }}</h3>
		<div class="role">{{ $interview->post }}</div>
		<div class="role">{{ $interview->company ? '' . $interview->company->name : '' }}</div>
		<p>{{ $interview->introtext }}</p>
		<footer>
			<span class="date">
				{{ $interview->published_at ? LocalizedCarbon::instance($interview->published_at)->formatLocalized('%d %f ‘%y') : '' }}
			</span>
			<span class="stats">
				<span class="stats-unit">
					<i class="icon icon-views"></i>
					{{ $interview->views or 0 }}
				</span>
				<span class="stats-unit">
					<i class="icon icon-comments"></i>
					{{ $interview->comments_count or 0 }}
				</span>
			</span>
		</footer>
	</div>
</a>
