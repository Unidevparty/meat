var editor_config =  {
	filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
	filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
	filebrowserBrowseUrl: '/laravel-filemanager?type=Images',
	filebrowserUploadUrl: '/laravel-filemanager/upload?type=Images&_token='
};


$('.datepicker').datepicker({
	autoclose: true,
	format: 'yyyy-mm-dd',
});

$('.more_lnk').click(function() {
	$($(this).attr('href')).slideToggle();

	return false;
});

$('input[type="checkbox"].minimal, input[type="radio"].minimal').iCheck({
	checkboxClass: 'icheckbox_minimal-blue',
	radioClass   : 'iradio_minimal-blue'
})



$('.delete').click(function() {
    return confirm('Вы действительно хотите удалить этот элемент?');
});

$('.confirm').click(function(){
	return confirm('Подтвердите выполнение действия');
});


$(".multiple_select").select2({
    tags: true
});
$(".multiple_select_strict").select2({
    //tags: true
});

$('.select-custom').select2();


$(window).bind('keydown', function(event) {
    if (event.ctrlKey || event.metaKey) {
        switch (String.fromCharCode(event.which).toLowerCase()) {
        case 's':
            event.preventDefault();
			$('.save-form').submit();
            break;
        }
    }
});

// $('.search_by_date').datetimepicker({
// 	locale: 'ru',
// 	format: 'YYYY-MM-DD'
// });

$('.datetime_mask').datetimepicker({
	locale: 'ru',
	format: 'YYYY-MM-DD HH:mm:ss'
});

$('.date_mask').datetimepicker({
	locale: 'ru',
	format: 'YYYY-MM-DD'
});


function init_cropper(cropper_wrap, width, height, run_cropper, source) {
	var cropper_name = $(cropper_wrap).parent().data('name');

	var cropper;

	var image = $('.image_crop', cropper_wrap).get(0);
	var image_input = $('.image_input', cropper_wrap);
	var image_file_input = $('.image_file_input', cropper_wrap);



	var cropper_settings = {
			aspectRatio: width / height,
			minCropBoxWidth: width,
			minCropBoxHeight: height,
			rotatable: 0,
			scalable: 1,
			zoomable: 0,
			zoomOnTouch: 0,
			background: 0,
			autoCrop: 1,
			responsive: true,
			restore: true,
			autoCropArea: 1,
			toggleDragModeOnDblclick: 1,
			crop: function(e) {
				setTimeout(function() {
					image_input.val(cropper.getCroppedCanvas({width: $(cropper_wrap).data('width'), height: $(cropper_wrap).data('height')}).toDataURL('image/jpeg'));
					$('.image_input', cropper_wrap).prop('disabled', false);
				}, 200);
				$('.crop_reset', cropper_wrap).show();
			}
		};

	set_image_crop_wrap_sizes(image, cropper_wrap);


	$('.crop_start', cropper_wrap).click(function() {console.log(cropper_name)

		var width = parseInt($(cropper_wrap).data('width'));
		var height = parseInt($(cropper_wrap).data('height'));


		$('.image_input', cropper_wrap).prop('disabled', false);

		$(this).hide()

		if (source) {
			$(image).attr('src', source);

			var source_img = new Image();
				source_img.onload = function() {
					set_image_crop_wrap_sizes(source_img, cropper_wrap);
				  	cropper = new Cropper(image, cropper_settings);
					// $('.image_input', cropper_wrap).prop('disabled', false);
					if (cropper_name) window[cropper_name] = cropper;
				};
				source_img.src = source;
		} else {
			cropper = new Cropper(image, cropper_settings);
			if (cropper_name) window[cropper_name] = cropper;
		}



		return false;
	});

	image_file_input.change(function() {
		var oFReader = new FileReader();
		oFReader.readAsDataURL(this.files[0]);
		oFReader.onload = function (oFREvent) {
			if (cropper) cropper.destroy();

			$(image).attr('src', this.result);

			$('.image_crop_wrap', cropper_wrap).show();

			setTimeout(function() {
				set_image_crop_wrap_sizes(image, cropper_wrap);

				cropper = new Cropper(image, cropper_settings);

				if (cropper_name) window[cropper_name] = cropper;

				// $('.image_input', cropper_wrap).prop('disabled', false);
			}, 200);

		}
	});



	$('.crop_reset', cropper_wrap).click(function() {

		$('.image_input', cropper_wrap).prop('disabled', true);

		cropper.destroy();
		image_input.val('');
		$('.crop_reset', cropper_wrap).hide();
		$('.crop_start', cropper_wrap).show();
		return false;
	});

	if (run_cropper) {
		cropper = new Cropper(image, cropper_settings);
		if (cropper_name) window[cropper_name] = cropper;
		return cropper;
	}
}

function set_image_crop_wrap_sizes(image, cropper_wrap) {
	var w = $(image).prop("naturalWidth");
	var h = $(image).prop("naturalHeight");

	var maxw = $(cropper_wrap).parent().width();
	var maxh = maxw * h / w;

	if (maxw < w) {
		w = maxw;
		h = maxh;
	}

	$('.image_crop_wrap', cropper_wrap)
		.width(w)
		.height(h);
}


$('[name="title"]').each(function() {
	chk_count($(this));
	$(this).on('keyup', function() {
		chk_count($(this));
	});
});

$('[name="keywords"], [name="description"]').each(function() {
	chk_count($(this));
	$(this).on('keyup', function() {
		chk_count($(this));
	});
});


function chk_count(element) {
	var l = element.val().length;
	var row = element.closest('.form-group');

	$('.l', row).text(l);
}



function init_multiple_cropper(wrap) {

	var source_image = $('[name="source"]', wrap).val();

	$('.form-group', wrap).each(function() {
		var cropper_wrap = this;
		var width = parseInt($(this).data('width'));
		var height = parseInt($(this).data('height'));

		init_cropper(cropper_wrap, width, height, 0, source_image);
	});



	$('.image_file_input', wrap).change(function() {

		var oFReader = new FileReader();
		oFReader.readAsDataURL(this.files[0]);
		oFReader.onload = function (oFREvent) {
			var file = this;

			$('.form-group', wrap).each(function() {
				var cropper_wrap = this;
				var image = $('.image_crop', cropper_wrap).get(0);
				var width = parseInt($(this).data('width'));
				var height = parseInt($(this).data('height'));

				$(image).attr('src', file.result);

				$('.image_crop_wrap', cropper_wrap).show();

				setTimeout(function() {
					set_image_crop_wrap_sizes(image, cropper_wrap);

					init_cropper(cropper_wrap, width, height, 1);
				}, 200);
			});
		}
	});
}
