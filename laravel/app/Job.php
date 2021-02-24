<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Job extends Model
{
    protected $fillable = [
		'name',
		'alias',
		'published_at',
		'published',
		'fixed',
		'fixed_to',
		'our',
		'closed',
		'close_id',
		'title',
		'keywords',
		'description',
		'company_type_id',
		'company_id',
		'introtext',
		'city',
		'address',
		'zarplata',
		'zp_options',
		'signature',
		'visibility',
		'obyazannosti',
		'trebovaniya',
		'usloviya',
		'views',
	];

	protected $attributes = [
		'name' => '',
		'alias' => '',
        'published' => 0,
		'company_type_id' => 0,
    ];

	protected $dates = [
        'published_at',
        'fixed_to'
    ];

	public function setNameAttribute($name) {
		$this->attributes['name'] = $name;
		$this->attributes['alias'] = str_slug($this->id . '-' . $name);
	}

	public function hasZpOption($option)
	{
		return strpos($this->zp_options, $option) !== FALSE;
	}

	public function company() {
    	return $this->belongsTo(Company::class);
    }

	public function company_type() {
    	return $this->belongsTo(CompanyType::class);
    }

    public function getIsfixedAttribute()
    {
        if ($this->fixed && $this->fixed_to >= Carbon::now()) {
            return true;
        }

        return false;
    }



	public function hasGroup($group_id)
	{
		return strpos($this->visibility, '|' . $group_id . '|') !== FALSE;
	}

	public function scopeLatest($query) {
		return $query->orderBy('fixed', 'desc')->orderBy('published_at', 'desc');
	}

	public function scopeNofixed($query) {
		return $query->where('fixed', 1)->where('fixed_to', '<', Carbon::now())->orWhere('fixed', 0);
	}

    public function scopeFixed($query)
    {
        return $query->where([
            ['fixed', '=',  1],
            ['fixed_to', '>=', Carbon::now()]
        ]);
    }

	public function scopePublished($query) {
		return $query->where('published_at', '<=', Carbon::now())->where('published', 1)->where('closed', 0);
	}
}
