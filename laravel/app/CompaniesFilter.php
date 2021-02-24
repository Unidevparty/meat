<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\CompanyType;
use App\Company;

class CompaniesFilter extends Model
{
    protected $fillable = [
        'name',
        'filter_string',
        'profiles',
        'country',
        'region',
        'city',
        'type',
    ];

    public function setProfilesAttribute($profiles)
    {
        $this->attributes['profiles'] = json_encode($profiles);
    }

    public function getProfilesAttribute()
    {
        return json_decode($this->attributes['profiles'], true);
    }

    public function set_filter_string()
    {
        $filter_string = [];

        if ($this->profiles) {
            $filter_string['profiles'] = $this->profiles;
        }

        if ($this->country) {
            $filter_string['country'] = $this->country;
        }

        if ($this->region) {
            $filter_string['region'] = $this->region;
        }

        if ($this->city) {
            $filter_string['city'] = $this->city;
        }

        $this->filter_string = http_build_query($filter_string);

        $this->save();
    }

    function getCompaniesCountAttribute() {

        $profiles = $this->profiles;
        $country  = $this->country;
        $region   = $this->region;
        $city     = $this->city;

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
        } else {
            $companies = Company::published();
        }

        if ($country) $companies->where('country', $country);
        if ($region) $companies->where('region', $region);
        if ($city) $companies->where('city', $city);

        return $companies->count();
    }
}
