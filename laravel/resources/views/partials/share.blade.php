<script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
<script src="//yastatic.net/share2/share.js"></script>
<?php $meta = getMeta(); ?>
@if ($meta)
	<div
		class="ya-share2"
		data-services="vkontakte,facebook,odnoklassniki,gplus,twitter,lj"
		
		@if ($title || $meta->title)
			data-title="{{ $title or $meta->title }}"
		@endif
		
		@if ($source_image)
			data-image="{{ $source_image ? 'https://meat-expert.ru' . $source_image : '' }}"
		@endif

		@if ($description || $meta->description)
			data-description="{{ $description or $meta->description }}"
		@endif
	></div>
@else
	<div
		class="ya-share2"
		data-services="vkontakte,facebook,odnoklassniki,gplus,twitter,lj" 
		@if ($title)
			data-title="{{ $title }}"
		@endif
		
		@if ($source_image)
			data-image="{{ $source_image ? 'https://meat-expert.ru' . $source_image : '' }}"
		@endif

		@if ($description)
			data-description="{{ $description }}"
		@endif
	></div>
@endif