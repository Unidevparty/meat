<div class="check_data">
	<h3>Анализ текста</h3>
	<p>
		Дата проверки: <strong>{{ $textru_data['result_json']['date_check'] }}</strong> <br>
		Униакльность: <strong>{{ $textru_data['text_unique'] }}</strong>
	</p>
	<p><a href="#unique" class="more_lnk">Подробнее</a></p>
	<div id="unique" class="more_data">
		<h4>Ссылки:</h4>
		<table class="table">
			@foreach ($textru_data['result_json']['urls'] as $url)
				<tr>
					<td><a href="{{ $url['url'] }}" target="_blank">{{ $url['url'] }}</a></td>
					<td>{{ $url['plagiat'] }}%</td>
				</tr>
			@endforeach
		</table>
	</div>
	<h4>Орфография:</h4>
	<p>Ошибки: <strong>{{ count($textru_data['spell_check']) }}</strong></p>
	<p><a href="#orfografy" class="more_lnk">Подробнее</a></p>
	<div id="orfografy" class="more_data">
		<div class="check_text_wrap">
			@php
				$txt = $textru_data['result_json']['clear_text'];

				foreach ($textru_data['spell_check'] as $err) {
					$txt = str_ireplace($err['error_text'], '<span class="error_word">' . $err['error_text'] . '</span>', $txt);
				}

				echo $txt;
			@endphp
		</div>
		<table class="table">
			<tr>
				<th>Тип ошибки</th>
				<th>Проблема</th>
				<th>Текст</th>
				<th>Варианты</th>
			</tr>
			@foreach ($textru_data['spell_check'] as $err)
				<tr>
					<td>{{ $err['error_type'] }}</td>
					<td>{{ $err['reason'] }}</td>
					<td>{{ $err['error_text'] }}</td>
					<td>{{ implode(' || ', $err['replacements']) }}</td>
				</tr>
			@endforeach
		</table>
	</div>
	<h4>SEO:</h4>
	<p>
		Заспамленность: <strong>{{ $textru_data['seo_check']['spam_percent'] }}</strong> <br>
		Вода: <strong>{{ $textru_data['seo_check']['water_percent'] }}</strong> <br>
		Всего символов: <strong>{{ $textru_data['seo_check']['count_chars_with_space'] }}</strong> <br>
		Без пробелов: <strong>{{ $textru_data['seo_check']['count_chars_without_space'] }}</strong> <br>
		Количество слов: <strong>{{ $textru_data['seo_check']['count_words'] }}</strong>
	</p>
	<p><a href="#seo" class="more_lnk">Подробнее</a></p>
	<div id="seo" class="more_data">
		<div class="check_text_wrap">
			@php
				$txt = $textru_data['result_json']['clear_text'];

				foreach ($textru_data['seo_check']['list_keys'] as $key) {
					$txt = str_ireplace($key['key_title'], '<span class="key_word">' . $key['key_title'] . '</span>', $txt);
				}

				echo $txt;
			@endphp
		</div>
		<h4>Ключевые слова</h4>
		<table class="table">
			@foreach ($textru_data['seo_check']['list_keys'] as $key)
				<tr>
					<td>{{ $key['key_title'] }}</td>
					<td>{{ $key['count'] }}</td>
				</tr>
			@endforeach
		</table>
		<h4>Ключевые слова по группам</h4>
		<table class="table">
			@foreach ($textru_data['seo_check']['list_keys_group'] as $key)
				<tr>
					<td>{{ $key['key_title'] }}</td>
					<td>{{ $key['count'] }}</td>
				</tr>
				@if (!empty($key['sub_keys']))
					@foreach ($key['sub_keys'] as $key2)
						<tr>
							<td style="padding-left: 50px;">{{ $key2['key_title'] }}</td>
							<td>{{ $key2['count'] }}</td>
						</tr>
					@endforeach
				@endif
			@endforeach
		</table>
	</div>
    {{-- <pre>
		{{ print_r($textru_data, 1) }}
	</pre> --}}
</div>