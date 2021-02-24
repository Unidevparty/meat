<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\CompaniesFilter;
use App\CompanyType;
use App\Company;

use View;

class CompanyController extends Controller
{
	protected $perpage = 10;

    public function index(Request $request) {
        $companies = $this->filter_companies()->take($this->perpage)->get();

        $more_link = $this->more_link();
        $filters = $this->filter_data();

        // К-во поставщиков
        $suppliers_count = Company::published()->where('supplier', 1)->count();
        // К-во производиетелй
        $manufacturers_count = Company::published()->where('manufacturer', 1)->count();
        // Страны
        $countries_count = Company::published()->select('country')->where('country', '<>', '')->groupBy('country')->get()->count();

        // $companies = Company::published();
        // Компании
        $companies_count = Company::published()->count();

        // $companies = $companies->get();

        $all_profiles = CompanyType::where('parent_id', 0)->with('childs')->get();
        $profiles = [];
        foreach ($all_profiles as $main_profile) {
            $profiles[$main_profile->id] = [
                'name' => $main_profile->name,
                'companies' => 0,
                'profiles' => []
            ];

            foreach ($main_profile->childs as $profile) {
                $profile_companies_count = $profile->companies->count();

                $profiles[$main_profile->id]['profiles'][] = [
                    'name' => $profile->name,
                    'companies' => $profile_companies_count
                ];

                $profiles[$main_profile->id]['companies'] += $profile_companies_count;
            }
        }

        $countries_list = Company::published()->where('country', '<>', '')->groupBy('country')->select(['country', \DB::raw('COUNT(id) as cnt')])->get()->toArray();
        $region_list    = Company::published()->where('region', '<>', '')->groupBy('region')->select(['region', \DB::raw('COUNT(id) as cnt')])->get()->toArray();
        $city_list      = Company::published()->where('city', '<>', '')->groupBy('city')->select(['city', \DB::raw('COUNT(id) as cnt')])->get()->toArray();

        $countries_map   = Company::countries_map();
        $company_filters = CompaniesFilter::all();



        return view('company.list', compact(
            'companies',
            'more_link',
            'filters',
            'suppliers_count',
            'manufacturers_count',
            'countries_count',
            'companies_count',
            'profiles',
            'countries_list',
            'region_list',
            'city_list',
            'countries_map',
            'company_filters'
        ));
    }

    public function more() {
        $page = (int) request()->get('page');
        $skip = (int) request()->get('from', -1);
        $page = $page > 1 ? $page - 1 : 0;

        $perpage  = intval(request()->get('perpage'));
        $perpage  = $perpage ? $perpage : $this->perpage;

        if ($skip < 0) $skip = $page * $perpage;

        $companies = $this->filter_companies()->skip($skip)->take($perpage)->get();


        $more_link = $this->more_link();

        return view('company.page', [
            'companies' => $companies,
            'more_link' => $more_link,
        ]);
    }

    public function show($alias)
    {
        $company = Company::where('alias', $alias);
        if (!member() || !member()->is_admin()) {
            $company = $company->published();
        }
        $company = $company->first();


        if (!$company) abort(404);

        // Увеличиваем просмотры
        \DB::table('companies')->where('id', $company->id)->update(['views' => $company->views + 1]);
        \DB::table('search')->where('searchable_id', $company->id)->where('searchable_type', Company::class)->update(['views' => $company->views + 1]);
        $company->views++; // Не сохраняем т.к. уже сохранили

        // $company->views += 1;
        // $company->save();

        $company_articles   = $company->articles()->published()->latest()->take(3)->get();
        $company_news       = $company->news()->published()->latest()->take(3)->get();
        $company_interviews = $company->interviews()->published()->latest()->take(3)->get();
        $company_jobs       = $company->jobs()->published()->latest()->take(3)->get();


        View::share('title', $company->title);
		View::share('keywords', $company->keywords);
		View::share('description', $company->description);
		View::share('source_image', $company->logo);
		View::share('canonical', url(route('company.show', $company->alias)));

        return view('company.show', [
            'company' => $company,
            'company_articles' => $company_articles,
            'company_news' => $company_news,
            'company_interviews' => $company_interviews,
            'company_jobs' => $company_jobs,
        ]);
    }

    protected function filter_companies() {
        $profiles = request()->get('profiles');
        $profile_names = request()->get('profile_names');
        $order_by = request()->get('order_by', 'name');

        $country  = request()->get('country');
        $region   = request()->get('region');
        $city     = request()->get('city');



        // Проверяем на левые поля
        if ($order_by != 'name' && $order_by != 'rating') {
            $order_by = 'name';
        }

        $order_dir = $order_by == 'name' ? 'asc' : 'desc';

        $companies = collect();


        if ($profiles) {
            $company_ids = [];
            $profiles = CompanyType::whereIn('id', $profiles)->get();

            foreach ($profiles as $profile) {
                $profile_companies = $profile->companies;

                foreach ($profile_companies as $company) {
                    $company_ids[] = $company->id;
                }
            }

            if (!$company_ids) return collect();

            $companies = Company::whereIn('id', $company_ids)->published();
        } elseif($profile_names) {
            $company_ids = [];
            $profiles = CompanyType::whereIn('name', $profile_names)->get();

            foreach ($profiles as $profile) {
                $profile_companies = $profile->companies;

                foreach ($profile_companies as $company) {
                    $company_ids[] = $company->id;
                }
            }

            if (!$company_ids) return collect();

            $companies = Company::whereIn('id', $company_ids)->published();
        } else {
            $companies = Company::published();
        }

        if ($country) $companies->where('country', $country);
        if ($region) $companies->where('region', $region);
        if ($city) $companies->where('city', $city);

        return $companies->orderBy($order_by, $order_dir);
    }

    protected function more_link() {
        $page           = (int) request()->get('page');
        $profile        = request()->get('profile');
        $profile_names  = request()->get('profile_names');
        $date           = request()->get('date');
        $order_by       = request()->get('order_by', 'name');
        $perpage        = intval(request()->get('perpage'));
        $perpage        = $perpage ? $perpage : $this->perpage;


        // Проверяем на левые поля
        if ($order_by != 'name' && $order_by != 'rating') {
            $order_by = 'name';
        }
        $page = $page > 0 ? $page : 1;


        $total = $this->filter_companies()->count();
        $current_count = $page * $perpage;

        if ($total <= $current_count) {
            return null;
        }

        $next_page = $page + 1;

        return route('company.more', [
            'page'          => $next_page,
            'profile'       => $profile,
            'profile_names' => $profile_names,
            'date'          => $date,
            'order_by'      => $order_by,
            'perpage'       => $perpage,
        ]);
    }

    protected function filter_data() {
        $companies = Company::published()->get();

        $order_by = request()->get('order_by', 'name');

        // Проверяем на левые поля
        if ($order_by != 'name' && $order_by != 'rating') {
            $order_by = 'name';
        }

        $profiles = [];

        foreach ($companies as $company) {
            foreach ($company->types as $type) {
                $profiles[$type->id] = $type->name;
            }
        }

        return [
            'profiles' => $profiles,
            'order_by' => $order_by,
            'companies' => $companies,
        ];
    }
}
