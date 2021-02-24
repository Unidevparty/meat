geocoder = {
    timer: null,
    geoobjects: null,
    apikey: '1de73f22-55ed-4f61-acae-bd21196fe16f',

    getAddress : function(address_element) {
        var address = address_element.val();

        delete $.ajaxSettings.headers["X-CSRF-TOKEN"];

        $.get({
            url: 'https://geocode-maps.yandex.ru/1.x/?format=json&apikey=' + geocoder.apikey + '&geocode=' + address,
            headers: {}
        }).done(function(data) {
            geocoder.geoobjects = data.response.GeoObjectCollection.featureMember;

            if (geocoder.geoobjects) {
                var geodata = [];

                for (var i = 0; i < geocoder.geoobjects.length; i++) {
                    geodata[i] = {
                        id: i,
                        text: geocoder.geoobjects[i].GeoObject.metaDataProperty.GeocoderMetaData.text
                    };
                }

                geocoder.autocomplete(address_element, geodata, function(elem, id) {
                    id = parseInt(id);

                    var addresses = geocoder.geoobjects[id].GeoObject.metaDataProperty.GeocoderMetaData.Address.Components;
                     window.point     = geocoder.geoobjects[id].GeoObject.Point.pos.split(' ');


                    // Страна
                    // регион
                    // Населенный пункт

                    var country,
                        locality;
                    var region = [];

                    for (var i = 0; i < addresses.length; i++) {
                        if (addresses[i].kind == 'country') {country = addresses[i].name;}
                        if (addresses[i].kind == 'province') {region[region.length] = addresses[i].name;}
                        if (addresses[i].kind == 'locality') {locality = addresses[i].name;}
                    }



                    // if (!locality) {
                    //     locality = region[region.length - 1];
                    // }



                    $('#coords').val(point[1] + ', ' + point[0]);
                    $('#country').val(country);
                    $('#region').val(region[0]);
                    $('#city').val(locality);
                });
            }
        });
    },

    autocomplete: function(input, data, callback) {
       $('.options').html('');
       var options = '';
       for (var i = 0; i < data.length; i++) {
           options += '<div class="option" data-id="' + data[i].id + '">' + data[i].text + '</div>';
       }

       $('.options').html(options);

       $('.autocomplete').show();

       $('.options .option').click(function() {
           var value = $(this).text();
           var id = $(this).data('id');
           input.val(value);

           $('.autocomplete').hide();

           callback(this, id);
       });
   },

   init: function(address_element) {
       address_element.on('keypress', function() {
           if (geocoder.timer) {
               clearTimeout(geocoder.timer);
           }

           geocoder.timer = setTimeout(function() {
               geocoder.getAddress(address_element);
           }, 500);
       });
   }
}
