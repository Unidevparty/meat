<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyType extends Model
{
	protected $fillable = [
		'name',
		'parent_id',
	];

	protected $attributes = [
        'name' => '',
        'parent_id' => 0,
    ];

    public function companies() {
        return $this->belongsToMany(\App\Company::class);
    }

    public function childs()
    {
        return $this->hasMany(CompanyType::class, 'parent_id');
    }
}
