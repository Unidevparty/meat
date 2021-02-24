    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/vue"></script> --}}

    <script type="text/javascript">
        var companies = new Vue({
            el: '#companies_wrap',
            data: {
                companies: [
        			@foreach ($filters['companies'] as $company)
        				@if ($company->coords)
        					{
        						name:'{{ $company->name }}',
        						loc:[{{ $company->coords }}],
        						profiles:[
        							@foreach ($company->types as $type)
        								"{{ $type->name }}",
        							@endforeach
        						],
        						country:"{{ $company->country }}",
        						region:"{{ $company->region }}",
        						city:"{{ $company->city }}",
        						url:"{{ route('company.show', $company->alias) }}",
                                views:"{{ $company->views }}",
                                image: "resize($company->logo, 214, 214, false)",
                                introtext: {!! json_encode($company->introtext) !!},
                                is_checked: {{ (int) $company->is_checked }},
                                updated_at: "{{ $company->updated_at }}",
        					},
        				@endif
        			@endforeach

        		],

                countries: [
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
                ],

                profiles: [
                    @foreach ($profiles as $main_profile)
                        {
                            name: '{{ $main_profile['name'] }}',
                            companies: {{ $main_profile['companies'] }},
                            profiles: [
                                @foreach ($main_profile['profiles'] as $profile)
                                    {
                                        name: '{{ $profile['name'] }}',
                                        companies: {{ $profile['companies'] }},
                                        disabled: {{ (int) !$profile['companies'] }}
                                    },
                                @endforeach
                            ]
                        },
                    @endforeach
                ],

                selectedProfiles: [],
                selectedCountry: null,
                selectedRegion: null,
                selectedCity: null
            },

            methods: {
                checkProfile: function(e) {
                    // console.log($(e.target).text());
                }
            }
        })
    </script>
