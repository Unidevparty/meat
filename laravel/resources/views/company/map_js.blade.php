

@section('scripts')
    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU" type="text/javascript"></script>

    <style>
        .placemark_layout_container {
        }
        .square_layout {
            color: #2d2d2d;
            font-size: 15px;
            white-space: nowrap;
            font-family: proxima, arial, sans-serif;
            height: 28px;
            line-height: 29px;
            border-radius: 28px;
            background: rgba(255,255,255,0.8);
            padding: 0 20px 0 37px;
            position: absolute;
            box-shadow: 0.5px 0.866px 3px 0px rgba(0, 0, 0, 0.25);
        }
        .square_layout:before {
            content: '';
            width: 21px;
            height: 21px;
            border-radius: 21px;
            background: #e85c5f;
            position: absolute;
            left: 5px;
            top: 0;
            bottom: 0;
            margin: auto;
        }
    </style>
	<script src="/js/jquery.jscrollpane.min.js"></script>
	<script type="text/javascript">
		var mapObj;
		var companiesObj=[
			@foreach ($filters['companies'] as $company)
				@if ($company->coords)
					{
						name:'{{ $company->name }}',
						loc:[{{ $company->coords }}],
						companyType:[
							@foreach ($company->types as $type)
								"{{ $type->name }}",
							@endforeach
						],
						country:"{{ $company->country }}",
						supplier: {{ $company->supplier }}, // Поставщик
						manufacturer: {{ $company->manufacturer }}, // Производитель
						region:"{{ $company->region }}",
						city:"{{ $company->city }}",
						url:"{{ route('company.show', $company->alias) }}"
					},
				@endif
			@endforeach

		];

        $('.map_companies_count').text(companiesObj.length); // Задаем к-во компаний

        var countries = [
            @foreach ($countries_map as $country => $regions)
                {
                    name: "{{ $country or 'Не указано' }}",
                    cities: [],
                    regions: [
                        @foreach ($regions as $region => $cities)
                            {
                                name: "{{ $region or 'Не указано' }}",
            					cities:[
                                    @foreach ($cities as $city => $city_companies)
            						    ["{{ $city or 'Не указано' }}", {{ $city_companies }}],
                                    @endforeach
            					]
                            },
                        @endforeach
                    ]
                },
            @endforeach
        ];


	   ymaps.ready(mapObj = init);

		$(function(){

			var availableCountries;
			var availableRegions;
			var availableCities;

			function getRegions(country, countriesArr){
				var countryObj = $.grep(countriesArr, function (countryObj) {
						return countryObj.name === country;
				})[0],
					regionTpl = '';
					countryObj.regions.map(function (region) {
						var qty = availableRegions.reduce(function(a,b){
    						if(b==region.name){
    							return ++a;
    						} else {
    							return a
    						}
    					},0)


						return regionTpl += '<li><a href="" data-filter-content="' + region.name + '" '+(availableRegions.filter((v, i, a) => a.indexOf(v) === i).includes(region.name)?" ":" class='disabled'")+'>' + region.name + ' <span class="qty">(' + qty + ')</span></a></li>';
				});
				$('[data-mapfilter="03"] .map-filter-regions').html(regionTpl);
				$('[data-mapfilter-call="03"]').removeClass('disabled');

				// $('[data-mapfilter="03"]').find('[data-filter-content]').each(function(){
				// 	if(availableRegions.includes($(this).attr("data-filter-content"))){
				// 		console.log($(this).attr("data-filter-content"));
				// 		$(this).removeClass('disabled')
				// 	} else {
				// 		$(this).addClass('disabled')
				// 	}
				// })



			}

			function getCitiesByCountry(country, countriesArr){
				var countryObj = $.grep(countriesArr, function (countryObj) {
						return countryObj.name === country;
				})[0],
					citiesTpl = '';
					countryObj.regions.map(function (region) {

						return region.cities.map(function (city) {
								var qty = availableCities.reduce(function(a,b){
		    						if(b==city[0]){
		    							return ++a;
		    						} else {
		    							return a
		    						}
		    					},0)
								return citiesTpl += '<li><a href="" data-filter-content="' + city[0] + '"'+(availableCities.filter((v, i, a) => a.indexOf(v) === i).includes(city[0])?" ":" class='disabled'")+'>' + city[0] + ' <span class="qty">(' + qty + ')</span></a></li>';
						});
				});
				$('[data-mapfilter="04"] .map-filter-regions').html(citiesTpl);
				$('[data-mapfilter="04"] .h').text(country);


			}

			function getCitiesByRegion(region, countriesArr){
				var citiesTpl = '',
					countryObj = countriesArr.map(function (country) {
						country.regions.map(function (reg) {
								if (reg.name === region) {
										reg.cities.map(function (city) {
												var qty = availableCities.reduce(function(a,b){
						    						if(b==city[0]){
						    							return ++a;
						    						} else {
						    							return a
						    						}
						    					},0)
												citiesTpl += '<li><a href="" data-filter-content="' + city[0] + '"'+(availableCities.filter((v, i, a) => a.indexOf(v) === i).includes(city[0])?" ":" class='disabled'")+'>' + city[0] + ' <span class="qty">(' + qty + ')</span></a></li>';
										});
								}
						});
				});
				$('[data-mapfilter="04"] .map-filter-regions').html(citiesTpl);
				$('[data-mapfilter="04"] .h').text(region);
				$('[data-mapfilter-call="04"]').removeClass('disabled')

			}

			function clearFilter(){
				$('.map-section').removeClass('empty-set');
				$('[data-mapfilter-call="03"], [data-mapfilter-call="04"]').addClass('disabled')
				$('[data-filter-content]').removeClass('selected');
				$('[data-mapfilter-call').each(function(){
					$(this).find('span').removeClass('not-empty').text($(this).attr('data-placeholder'))
				})
			}


			$('.clear-filter').click(function(e){
				e.preventDefault();
				clearFilter();
			})

			if($(window).width()<768){
				var jspEls=[];
				$('.mb-scroll-cnt').each(function(){
					jspEls.push($(this).jScrollPane(
						{
							autoReinitialise: true
						}
					).data().jsp)}
				);
				$('.map-filter-tab').on('click','.close',function(e){
					e.preventDefault();
					$(this).parents('.map-filter-tab').hide();
					$('html').removeClass('filter-called');

				})
			}
			$('[data-mapfilter-call]').click(function(e){
				e.preventDefault();
				if($(this).is(':not(.disabled)')){
					if($(window).width()>767){
						$('[data-mapfilter-call="'+$(this).attr('data-mapfilter-call')+'"]').toggleClass('called').siblings().removeClass('called');
						$('[data-mapfilter="'+$(this).attr('data-mapfilter-call')+'"]').slideToggle().siblings('[data-mapfilter]').slideUp();;
						if (typeof(jspEls) !== 'undefined' && jspEls.length) {
							$.each(
								jspEls,
								function(i) {
									this.destroy();
								}
							)
							jspEls = [];
						}
					}
					else{
						$('[data-mapfilter="'+$(this).attr('data-mapfilter-call')+'"]').show();
						$('html').addClass('filter-called');
					}
				}
			})

			$('[data-quick-filter]').click(function(e){
				e.preventDefault();
				var vals=$(this).attr('data-quick-filter').split(';')
				//var searchObj={}
				for(var i=0;i<vals.length;i++){
					console.log(vals[i].split(':')[0].trim());
					$('[data-filter="'+vals[i].split(':')[0].trim()+'"]').attr('data-filter-value', vals[i].split(':')[1].trim()).find('span').text(vals[i].split(':')[1].trim());
				}
				$('#callFilter').click();

			})

			$('#callFilter').click(function(){
				$('[data-mapfilter]').slideUp();
				$('[data-mapfilter-call]').removeClass('called');
                loadCompanies();
			})

			$('.map-filter').on('click', '[data-filter-content]', function(e){

	    		e.preventDefault();
	    		var cnt=parseInt($(this).find('.qnt').text().replace("(", "").replace(")", ""));
	    		if(cnt==0){
	    			return false;
	    		}
	    		$(this).toggleClass('selected');
	    		if($(this).parents('.h').length && $(this).hasClass('selected')){
	    			$(this).parents('.h').siblings('ul').find('[data-filter-content]').addClass('selected');
	    		} else {
	    			$(this).parents('.h').siblings('ul').find('[data-filter-content]').removeClass('selected');
	    		}

	    		if($(this).parents('ul').siblings('.h').find('[data-filter-content]').length){

	    			if($(this).parent('li').parent('ul').find('.selected').length<1){
	    				$(this).parents('ul').siblings('.h').find('[data-filter-content]').removeClass('selected');
	    			}


	    			if($(this).parent('li').parent('ul').find('.selected').length==$(this).parent('li').parent('ul').find('li').length){
	    				$(this).parents('ul').siblings('.h').find('[data-filter-content]').addClass('selected');
	    			}

	    		}

	    		var filterCategory=$(this).parents('[data-mapfilter]').attr('data-mapfilter');

	    		$('[data-mapfilter-call="'+filterCategory+'"]').nextAll('.filter-caller').each(function(){
	    			$(this).attr('data-filter-value','').find('span').text($(this).attr('data-placeholder')).removeClass('not-empty')
					$('[data-mapfilter="'+$(this).attr('data-mapfilter-call')+'"]').find('.selected[data-filter-content]').removeClass('selected');

					if(!$(this).find('span').hasClass('not-empty')){
						$(this).nextAll('.filter-caller').addClass('disabled');
					}
	    		})

	    		if(!$(this).parents('[data-filter-multiple]').length){
	    			$('[data-mapfilter-call="'+filterCategory+'"]')
	    					.attr('data-filter-value', $(this).attr('data-filter-content'))
	    					.removeClass('called')
	    					.find('span').text($(this).attr('data-filter-content'))
	    					.addClass('not-empty');
	    			$(this).parents('li').siblings().find('.selected').removeClass('selected');
	    			if($(window).width()>767){
		    			$(this).parents('[data-mapfilter]').slideUp();
	    			} else {
		    			$(this).parents('[data-mapfilter]').hide();
		    			$('html').removeClass('filter-called');
	    			}
	    			// perform action
	    			//

	    			if(filterCategory=='02'){
	    				getRegions($(this).attr('data-filter-content'), countries);
	    				getCitiesByCountry($(this).attr('data-filter-content'), countries);

	    			}
	    			if(filterCategory=='03'){
	    				getCitiesByRegion($(this).attr('data-filter-content'), countries);
	    			}

	    		} else {
	    			var str='', arr=$(this).parents('[data-filter-multiple]').find('li li a.selected');
	    			if(arr.length>=1){
	    				$('[data-mapfilter-call="02"]').removeClass('disabled')
	    				$(this).parents('[data-filter-multiple]').addClass('has-variants')
	    			} else{
	    				$('[data-mapfilter-call="02"]').addClass('disabled')
	    				$(this).parents('[data-filter-multiple]').removeClass('has-variants')
	    			}

	    			arr.each(function(){
	    				str+=$(this).attr('data-filter-content');
	    				var $this = $(this);
					    if($this[0] != arr.last()[0]) {
					        str+=" | ";
					    }


	    			})
	    			//console.log(str.split(' | '));


	    			var available = companiesObj.filter(function (elem) {
	    			 var _elem=elem.companyType.filter(function (x) {
	    			   if(str.split(' | ').includes(x)){
	    			   	return true;
	    			   }
	    			 });
	    			 if(_elem.length){
	    			 	return elem
	    			 }
	    			});
	    			availableCountries=available.map(x=>x.country)//.filter((v, i, a) => a.indexOf(v) === i),
	    			,availableRegions=available.map(x=>x.region)//.filter((v, i, a) => a.indexOf(v) === i),
	    			,availableCities=available.map(x=>x.city)//.filter((v, i, a) => a.indexOf(v) === i);


	    			$('[data-mapfilter="02"]').find('[data-filter-content]').each(function(){
	    				var current=$(this).attr("data-filter-content");
	    				if(availableCountries.filter((v, i, a) => a.indexOf(v) === i).includes(current)){
	    					$(this).removeClass('disabled');
	    					var itemsAvailable = availableCountries.reduce(function(a,b){
	    						if(b===current){
	    							return ++a;
	    						} else {
	    							return a
	    						}
	    					},0)
	    					$(this).find('strong').text(itemsAvailable);
	    				} else {
	    					$(this).addClass('disabled')
	    					$(this).find('strong').text('0');
	    				}
	    			})



	    			$('[data-mapfilter-call="'+filterCategory+'"]')
	    				.attr('data-filter-value', str)
	    				.find('span').text(str)
	    				.addClass('not-empty');

	    		}
	    	})
		})

		function init(){
			var myMap = new ymaps.Map("map", {
				center: [55.76, 37.64],
				zoom: 7,
				controls: []
			});
			myMap.controls.add('zoomControl', {
				size: 'small',
				float: 'none',
				position: {
					bottom: '84px',
					right: '30px'
				}
			});
			if(Modernizr.touch) {
				myMap.behaviors.disable('drag');
			}
			myMap.behaviors.disable('scrollZoom');
			//myMap.behaviors.disable('multiTouch');

			myMap.controls.add('fullscreenControl', {
				size: 'small',
				float: 'none',
				position: {
					bottom: '50px',
					right: '30px'
				}
			});
			var meatPm = ymaps.templateLayoutFactory.createClass('<div class="placemark_layout_container"><div class="square_layout"><span data-href="' + '@{{ properties.iconUrl }}' + '">' + '@{{ properties.iconCaption }}' + '</span></div></div>');
			'use strict';

			var filterRun = document.getElementById('callFilter');
			filterRun.addEventListener('click', function (e) {
				e.preventDefault();
				var bounds = new Array(),
					filterRules = {},
					myCollection = new ymaps.GeoObjectCollection(),
					filterDiv = document.querySelectorAll('#mapFilter [data-filter-value]:not([data-filter-value=""])');
				var _iteratorNormalCompletion = true;
				var _didIteratorError = false;
				var _iteratorError = undefined;

				try {
					for (var _iterator = filterDiv[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
						filterValue = _step.value;

						filterRules[filterValue.dataset.filter] = filterValue.dataset.filterValue.split(' | ');
					}
					// console.log(filterRules);
				} catch (err) {
					_didIteratorError = true;
					_iteratorError = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion && _iterator.return) {
							_iterator.return();
						}
					} finally {
						if (_didIteratorError) {
							throw _iteratorError;
						}
					}
				}

				myMap.geoObjects.removeAll();
				var filteredCompanies = companiesObj.filter(function (company) {
					for (var key in filterRules) {
						if (typeof company[key] === "string") {
							if (!filterRules[key].includes(company[key])) return false;
						} else {
							if (!company[key].some(function (r) {
								return filterRules[key].indexOf(r) >= 0;
							})) return false;
						}
					}
					return company;
				});
				// console.log(filteredCompanies);

				var _iteratorNormalCompletion2 = true;
				var _didIteratorError2 = false;
				var _iteratorError2 = undefined;

				try {
					for (var _iterator2 = filteredCompanies[Symbol.iterator](), _step2; !(_iteratorNormalCompletion2 = (_step2 = _iterator2.next()).done); _iteratorNormalCompletion2 = true) {
						company = _step2.value;

						$('html').append('<div id="tempMeasureDiv" style="position:fixed;left:-9999px;top:-9999px;font-size: 15px;padding: 0 20px 0 37px;white-space: nowrap;font-family: proxima, arial, sans-serif;">'+company.name+'</div');

						var pl = new ymaps.Placemark(company.loc, {
							iconCaption: company.name,
							iconUrl: company.url
						}, {
							iconLayout: meatPm,
							//iconOffset: [-14, -14],
							iconShape: {
								type: 'Rectangle',
								coordinates: [
									[0, 0], [parseInt($('#tempMeasureDiv').outerWidth()), 28]
								]
							}
						});
						pl.events.add('click', function(event){
							location.href=event.originalEvent.target.properties._data.iconUrl;
						})
						myCollection.add(pl);
						$('#tempMeasureDiv').remove();
					}
				} catch (err) {
					_didIteratorError2 = true;
					_iteratorError2 = err;
				} finally {
					try {
						if (!_iteratorNormalCompletion2 && _iterator2.return) {
							_iterator2.return();
						}
					} finally {
						if (_didIteratorError2) {
							throw _iteratorError2;
						}
					}
				}
				if(myCollection.getLength()){
					setTimeout(function(){
						$('.map-section').removeClass('empty-set');
						myMap.geoObjects.add(myCollection);
						var centerAndZoom = ymaps.util.bounds.getCenterAndZoom(myCollection.getBounds(), myMap.container.getSize(), myMap.options.get('projection'));
							myMap.setBounds(myCollection.getBounds(), { checkZoomRange: true, zoomMargin: [100, 20, 20, 20] }).then(function(){ if(myMap.getZoom() > 14) myMap.setZoom(14);});
					},400)
				}
				else{
					$('.map-section').addClass('empty-set');
				}
			});


			return myMap;
		}





		$('.perpage_wrap a').click(function() {
			var perpage = parseInt($(this).data('perpage'));

			changePerPage(perpage);

			$('.perpage_wrap a').removeClass('active');
			$(this).addClass('active');

			return false;
		});


        $('a[data-order_by]').click(function() {
            $('a[data-order_by]').removeClass('active');
            $(this).addClass('active');

            loadCompanies();

            return false;
        });


		function changePerPage(perpage) {
            var loadmore_link = $('.load_more');

            if (!loadmore_link.length) {
                return;
            }

			var url = loadmore_link.attr('href');
			var url_parts = url.split('&');

			perpage = perpage || 10;
			var curPerpage = perpage;

			for (i= 0; i < url_parts.length; i++) {
				if (url_parts[i].indexOf('perpage=') == 0) {
					curPerpage = parseInt(url_parts[i].replace('perpage=', ''));
					break;
			    }
			}

			url = url.replace('perpage=' + curPerpage, 'perpage=' + perpage);

			loadmore_link.attr('href', url);
		}

		// $('body').on('click', '.placemark_layout_container', function(e){
		// 	e.preventDefault();
		// 	e.stopPropagation();
		// 	console.log('aaaa');
		// })

        function getSelectedFilters() {
            var profiles = $('div[data-mapfilter-call="01"]').attr('data-filter-value').split(' | ');
            var country  = $('div[data-mapfilter-call="02"]').attr('data-filter-value');
            var region   = $('div[data-mapfilter-call="03"]').attr('data-filter-value');
            var city     = $('div[data-mapfilter-call="04"]').attr('data-filter-value');

            // console.log(profiles, country, region, city);

            var url = '';

            if (profiles.length && profiles[0]) {
                for (var i = 0; i < profiles.length; i++) {
                    url += '&profile_names[]=' + profiles[i];
                }
            }

            if (country) {
                url += '&country=' + country;
            }

            if (region) {
                url += '&region=' + region;
            }

            if (city) {
                url += '&city=' + city;
            }

            return url;
        }

        function getUrlParams(search) {
            let hashes = search.slice(search.indexOf('?') + 1).split('&')
            let params = {}
            hashes.map(hash => {
                let [key, val] = hash.split('=');

                key = key.replace('[]', '');

                if (typeof params[key] == 'undefined') {
                    params[key] = decodeURIComponent(val)
                } else if (typeof params[key] == 'string') {
                    params[key] = [params[key], decodeURIComponent(val)];
                } else {
                    params[key][params[key].length] = decodeURIComponent(val);
                }
            })

            return params
        }

        function loadCompanies() {
            var filters  = getSelectedFilters();
            var perpage  = $('.perpage_wrap a.active').attr('data-perpage');
            var order_by = $('[data-order_by].active').attr('data-order_by');
            var url = location.pathname + '/more?page=1&perpage=' + perpage + '&order_by=' + order_by + filters;

            $.get(url, function(data) {
                $('.company-card').remove();
    			$('.load_more').remove();

    			$(data).insertBefore('.load_more_before');

                $('.company-card').eq(0).appendTo('.content_section_top');
                $('.company-card').eq(1).appendTo('.content_section_top');

    			setTimeout(load_more, 200);
    		})
        }



	</script>

    {{-- @include('company.vue') --}}
@endsection
