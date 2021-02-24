var flist = [];

var group = $('.gallery_container').sortable({
  group: 'gallery_container',
  placeholder: '<div class="placeholder"></div>',
  itemSelector: '.photo_row',
  handle: '.drag_btn',
  // onMousedown: function() {
  //
  // },
  onDrop: function ($item, container, _super, event) {
      $item.removeClass('dragged').removeAttr("style");
      $("body").removeClass('dragging');

      resetSortOrder();

      $('.photo_description').each(function() {
          var id = $(this).attr('id');
          if (CKEDITOR.instances[id]) CKEDITOR.instances[id].destroy();

          CKEDITOR.replace(this, editor_config);
      });
    },
});

CKEDITOR.replace('text', editor_config);

init_editor();

$('.image-lfm').filemanager('image&wdir=/uploads/photogallery');


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
    $('.photo_description').each(function() {
        var id = $(this).attr('id');
        if (!CKEDITOR.instances[id]) {
            CKEDITOR.replace(this, editor_config);
        }
    });
}

function resetSortOrder() {
    $('.gallery_container .photo_row').each(function(i) {
        $('.sort_order', $(this)).val(i);
    });
}
