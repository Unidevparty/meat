{!! Form::open(['route' => 'subscribe', 'class' => 'subscribe-side-form side-box ajax_form']) !!}
	<div class="section-h">	Подписаться на новости</div>
	<div class="form-row"><input type="email" placeholder="Введите ваш E-mail" name="email" class="input-text"></div>
	<div class="form-row">
		<button class="btn btn-reg btn-red"><i class="icon icon-send"></i> отправить</button>
	</div>
{!! Form::close() !!}