<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JobClose extends Model
{
	protected $fillable = [
		'name',
	];

	protected $attributes = [
        'name' => '',
    ];
}
