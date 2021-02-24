<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
		'text',
		'member_id',
		'page_id',
		'type',
    ];

    public function getMemberAttribute()
    {
    	return \IPS\Member::load($this->member_id);
    }
}
