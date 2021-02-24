<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use \App\Company;

class CompanyReview extends Model
{
    protected $fillable = [
        'rate',
        'title',
        'text',
        'company_id',
        'member_id',
        'likes',
        'dislikes',
    ];

    public function getMemberAttribute()
    {
    	return \IPS\Member::load($this->member_id);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
