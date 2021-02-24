// Страница компаний

// Подгрузка галереи
$('.company_load_more_gallery').click(function() {
    var page = company_gallery_more.splice(0, 8);

    for (var i = 0; i < page.length; i++) {
        $('<div class="photo-thumb">' +
            '<a href="' + page[i].photo + '">' +
                '<img src="' + page[i].thumb + '" alt="' + page[i].name + '">' +
                '<span class="h">' + page[i].name + '</span>' +
            '</a>' +
        '</div>').insertBefore(this);
    }

    if (!company_gallery_more.length) {
        $(this).hide();
    }

    return false;
});

// Подгрузка видео
$('.company_load_more_video').click(function() {
    var page = company_videos_more.splice(0, 8);

    for (var i = 0; i < page.length; i++) {
        $('<div class="photo-thumb video-thumb">' +
                '<a href="' + page[i].url + '">' +
                    '<img src="' + page[i].thumb + '" alt="' + page[i].name + '">' +
                    '<span class="h">' + page[i].name + '</span>' +
                '</a>' +
        '</div>').insertBefore(this);
    }

    if (!company_videos_more.length) {
        $(this).hide();
    }

    return false;
});

// Подгрузка файлов
$('.company_load_more_files').click(function() {
    var page = company_files_more.splice(0, 5);

    for (var i = 0; i < page.length; i++) {
        $('<div class="file-item">' +
            '<a href="' + page[i].file + '">' +
                '<div class="h">' + page[i].name + '</div>' +
                '<span class="size">Файл ' + page[i].ext + ' - ' + page[i].size + '</span>' +
                '<span class="date">' + page[i].date + '</span>' +
                '<i class="icon icon-file"></i><i class="icon icon-dl"></i>' +
            '</a>' +
        '</div>').insertBefore(this);
    }

    if (!company_files_more.length) {
        $(this).hide();
    }

    return false;
});


// Фильтры
$('form.filter select').on('selectmenuchange', function() {
	$(this).closest('form').submit();
});