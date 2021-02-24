$('body').on('click', '.add-multiple-row', function() {
    var table = $(this).closest('table');

    var tr = $('tr', table).last().clone();

    $('input', tr).each(function() {
        $(this).val('');
    });

    $('tbody', table).append(tr);
});

$('body').on('click', '.remove-multiple-row', function() {
    var tbody = $(this).closest('tbody');

    if ($('tr', tbody).length > 1) {
        $(this).closest('tr').remove();
    } else {
        $('input', $(this).closest('tr')).each(function() {
            $(this).val('');
        });
    }
});

$('#company_form').submit(function() {
    var form = $(this);
    var name = $('[name="name"]', form);

    if(!name.val()) {
        name.parent().addClass('has-error');

        $('html, body').animate({
           scrollTop: 50
        }, 500);

        return false;
    }
})




$(function () {
    CKEDITOR.replace('text', editor_config);
    // CKEDITOR.replace('contacts', editor_config);
});

geocoder.init($('[name="address"]'));


$('#is_holding').click(function() {
    var checked = $('input', $(this)).is(':checked');
    $('#holding_id').prop('disabled', checked);
});

// $('.lfm').filemanager('image&wdir=/uploads/authors');


init_videos();
init_files();
init_brand();

/** ФОТОГАЛЕРЕЯ **/

// init_gallery();
// $('.add_gallery_row').click(function() {
//     var last_row = $('.gallery .gallery_row').last().clone()
//     var id = parseInt($(last_row).data('id')) + 1;
//
//     row = clear_gallery_row(last_row, id);
//
//     $('.gallery table').append(row);
//
//     init_gallery();
//
//     return false;
// });
//
// function init_gallery() {
//     $('.gallery .lfm').filemanager('image&wdir=/uploads/company');
//
//     $('.delete_gallery_row').click(function() {
//
//         if ($('.gallery_row').length > 1) {
//             $(this).closest('.gallery_row').remove();
//         } else {
//             var row = $('.gallery_row').eq(0);
//             clear_gallery_row(row, 0);
//         }
//
//         return false;
//     });
//
//     $('.gallery_photo_input').change(function() {
//         $('img', $(this).closest('tr')).attr('src', $(this).val());
//     });
// }
//
// function clear_gallery_row(row, id) {
//     $(row).attr('data-id', id);
//     $('img', row).attr('src', '');
//     $('.lfm', row).data('input', 'photo' + id);
//     $('.gallery_photo_input', row)
//         .attr('id', 'photo' + id)
//         .attr('name', 'gallery[' + id + '][photo]')
//         .val('');
//
//     $('.gallery_photo_name', row)
//         .attr('name', 'gallery[' + id + '][name]')
//         .val('');
//
//     return row;
// }

/** ВИДЕО **/

$('.videos').on('change', '.video_url', function() {
    var row       = $(this).closest('tr');
    var video_url = $(this).val().trim();
    var video_parts = video_url.split('?v=');
    var video_key   = video_parts[video_parts.length - 1];

    var image = $('img', row);

    if (!video_url || !video_key) {
        image.attr('src', '');
        return;
    }

    video_image = 'https://img.youtube.com/vi/' + video_key + '/default.jpg';

    image.attr('src', video_image);
});




$('.add_video_row').click(function() {
    var last_row = $('.videos .video_row').last().clone()
    var id = parseInt($(last_row).data('id')) + 1;

    row = clear_video_row(last_row, id);

    $('.videos table').append(row);

    init_videos();

    return false;
});


function clear_video_row(row, id) {
    $(row).attr('data-id', id);
    $('img', row).attr('src', '');
    $('.lfm', row).data('input', 'photo' + id);
    $('.video_photo_input', row)
        .attr('id', 'photo' + id)
        .attr('name', 'videos[' + id + '][photo]')
        .val('');

    $('.video_url', row)
        .attr('name', 'videos[' + id + '][url]')
        .val('');

    $('.video_name', row)
        .attr('name', 'videos[' + id + '][name]')
        .val('');

    return row;
}

function init_videos() {
    $('.videos .lfm').filemanager('image&wdir=/uploads/company');

    $('.delete_video_row').click(function() {

        if ($('.video_row').length > 1) {
            $(this).closest('.video_row').remove();
        } else {
            var row = $('.video_row').eq(0);
            clear_video_row(row, 0);
        }

        return false;
    });

    $('.video_photo_input').change(function() {
        $('img', $(this).closest('tr')).attr('src', $(this).val());
    });
}


/** ФАЙЛЫ **/
$('.add_file_row').click(function() {
    var last_row = $('.files .file_row').last().clone()
    var id = parseInt($(last_row).data('id')) + 1;

    row = clear_file_row(last_row, id);

    $('.files table').append(row);

    init_files();

    return false;
});

function clear_file_row(row, id) {
    $(row).attr('data-id', id);
    $('img', row).attr('src', '');
    $('.filelfm', row).data('input', 'file' + id);
    $('.file_input', row)
        .attr('id', 'file' + id)
        .attr('name', 'files[' + id + '][file]')
        .val('');

    $('.file_name', row)
        .attr('name', 'files[' + id + '][name]')
        .val('');

    return row;
}

function init_files() {
    $('.files .filelfm').filemanager('file');

    $('.delete_file_row').click(function() {
        if ($('.file_row').length > 1) {
            $(this).closest('.file_row').remove();
        } else {
            var row = $('.file_row').eq(0);
            clear_file_row(row, 0);
        }

        return false;
    });

    $('.file_input').change(function() {
        $('img', $(this).closest('tr')).attr('src', $(this).val());
    });
}


/** Торговые марки **/

$('.add_brand_row').click(function() {
    var last_row = $('.brand .brand_row').last().clone()
    var id = parseInt($(last_row).data('id')) + 1;

    row = clear_brand_row(last_row, id);

    $('.brand table').append(row);

    init_brand();

    return false;
});

function init_brand() {
    $('.brand .lfm').filemanager('image&wdir=/uploads/company');

    $('.delete_brand_row').click(function() {

        if ($('.brand_row').length > 1) {
            $(this).closest('.brand_row').remove();
        } else {
            var row = $('.brand_row').eq(0);
            clear_brand_row(row, 0);
        }

        return false;
    });

    $('.brand_photo_input').change(function() {
        $('img', $(this).closest('tr')).attr('src', $(this).val());
    });
}

function clear_brand_row(row, id) {
    $(row).attr('data-id', id);
    $('img', row).attr('src', '');
    $('.lfm', row).data('input', 'photo' + id);
    $('.brand_photo_input', row)
        .attr('id', 'photo' + id)
        .attr('name', 'brands[' + id + '][photo]')
        .val('');

    $('.brand_photo_name', row)
        .attr('name', 'brands[' + id + '][name]')
        .val('');

    return row;
}



// Фотогалерея
var flist = [];

var group = $('.gallery_container').sortable({
  group: 'gallery_container',
  placeholder: '<div class="placeholder"></div>',
  itemSelector: '.photo_row',
  handle: '.drag_btn',

  onDrop: function ($item, container, _super, event) {
      $item.removeClass('dragged').removeAttr("style");
      $("body").removeClass('dragging');

      resetSortOrder();

      // $('.photo_description').each(function() {
      //     var id = $(this).attr('id');
      //     if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy();
      //
      //     CKEDITOR.replace(this, editor_config);
      // });
    },
});


init_editor();


$('#photos').change(preview_image);

$('.delete_gallery_row').click(function() {
    if ($('.photo_row').length > 1) {
        $(this).closest('.photo_row').remove();
    } else {
        var row = $('.photo_row').eq(0);
        clear_gallery_row(row, 0);
    }

    return false;
});






function preview_image() {

    // Вычисляем id
    id = flist.length;

    var file_input = document.getElementById("photos");
    for (var i = 0; i < file_input.files.length; i++) {
        var file = file_input.files[i];
        var block = $('.proto .photo_row').clone();

        block = create_photo_row(block, file, id + i)

        $('.gallery_container').append(block);

        flist.push(file);

        $('.delete_gallery_row', block).click(function(e) {
            var block = $(this).closest('.photo_row');
            var id = parseInt(block.data('id'));
            block.remove();
            flist[id] = false;

            return false;
        });
    }

    init_editor();

    resetSortOrder();
}


function create_photo_row(row, photo, id) {
    $(row).attr('data-id', id);
    $('img', row).attr('src', URL.createObjectURL(photo));

    $('.photo_description', row)
        .attr('name', 'new_photos[' + id + '][description]')
        .attr('id', 'new_photo_description_' + id)
        .attr('style', '')
        .val('');

    $('.sort_order', row).val(id).attr('name', 'new_photos[' + id + '][sort]');

    $('.cke', row).remove();

    return row;
}

function init_editor() {
    // $('.photo_description').each(function() {
    //     var id = $(this).attr('id');
    //     if (!CKEDITOR.instances[id]) {
    //         CKEDITOR.replace(this, editor_config);
    //     }
    // });
}

function resetSortOrder() {
    $('.gallery_container .photo_row').each(function(i) {
        $('.sort_order', $(this)).val(i);
    });
}


$('form').submit(function() {
    $('.submit_btn').prop('disabled', true);
    var form = $(this);

    for ( instance in CKEDITOR.instances ) {
        CKEDITOR.instances[instance].updateElement();
    }


    var data = new FormData( form[0] );
    var action = $(this).attr('action');

    data.delete('gallery_photos[]');

    $.each(flist, function(i, file){
        if (file) data.append('new_photos[' + i + '][file]', file);
    });

    $.ajax({
        url: action,
        type: 'POST',
        data: data,
        processData: false,
        contentType: false
    }).done(function(data) {
        if (typeof data.redirect != 'undefined' && data.redirect) {
            location.href = data.redirect;
        }

        $('.submit_btn').prop('disabled', false);
    }).fail(function() {
        alert('Произошла ошибка');

        $('.submit_btn').prop('disabled', false);
    });

    return false;
});
