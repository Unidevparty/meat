@if ($page->comments->count())
<div class="content-section" id="comments">
		<div class="section-header">
			<div class="h">Комментарии</div>
		</div>
		<div class="comment-thumbs">
			@foreach ($page->comments as $comment)
				<div class="blog-thumb comment-thumb">
					@if (member() && member()->is_admin())
					<a href="{{ route('comment.delete', $comment->id) }}" class="comment_delete">Удалить</a>
					@endif
					<p>{!! nl2br(replace_url($comment->text)) !!}</p>
					<footer>
						<div class="author-details">
							<figure><img src="{{ resize($comment->member->photo, 34, 34) }}" alt="{{ $comment->member->name }}"></figure>
							<a href="{{ $comment->member->url() }}" class="author">{{ $comment->member->name }}</a>
							<span class="date">{{ LocalizedCarbon::instance($comment->created_at)->formatLocalized('%d %f ‘%y') }}</span>
						</div>
					</footer>
				</div>
			@endforeach
		</div>
</div>
@endif

@if (member())
	<div class="content-section">
		<div class="section-header">
			<div class="h">Оставить комментарий</div>
		</div>

		<div class="error">{{ $errors->has('text') ? 'Напишите комментарий' :'' }}</div>

		{!! Form::open(['route' => 'comment.store', 'class' => 'comment-form']) !!}
			<input type="hidden" name="type" value="{{ $type }}">
			<input type="hidden" name="page_id" value="{{ $page->id }}">
			<div class="author-header author-details">
				<figure><img src="{{ resize(member()->photoUrl, 34, 34) }}" alt="{{ member()->name }}"></figure>
				<a href="{{ member()->profileUrl }}" class="author">{{ member()->name }}</a>
			</div>
			<div class="comment"><textarea name="text" placeholder="Ваш комментарий"></textarea></div>
			<footer class="tar">
				<input type="submit" class="btn btn-reg btn-red" value="отправить">
			</footer>
		{!! Form::close() !!}
	</div>
@endif
