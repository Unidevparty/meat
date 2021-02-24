<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $fillable = [
    	'key',
    	'value'
    ];

    protected $attributes = [
        'value' => '',
    ];

    public function scopebyKey($query, $key) {
		return $query->where('key', $key)->first();
	}

	public function scopeGetByKey($query, $key)
	{
		$param = $query->where('key', $key)->first();

		if ($param) {
			return $param->value;
		}

		return null;
	}
}
