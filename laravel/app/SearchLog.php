<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SearchLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'ip',
        'username',
        'query',
        'added_at',
    ];

    protected $dates = [
		'added_at',
	];
}
