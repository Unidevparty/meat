<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompanyHolding extends Model
{
    protected $fillable = [
		'name',
	];

	protected $attributes = [
        'name' => '',
    ];

    public function companies() {
        return $this->hasMany(\App\Company::class);
    }
}
