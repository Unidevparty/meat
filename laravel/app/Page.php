<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Page extends Model
{
    protected $fillable = [
    	'name',
    	'template',
		'url',
		'title',
		'description',
		'keywords',
		'text',
		'views',
		'published_at',
		'published',
		'textru_uid',
		'textru',
    ];

    protected $dates = ['published_at'];

    public function scopePublished($query) {
		return $query->where('published_at', '<=', Carbon::now())->where('published', 1)->latest();
    }

    public function setUrlAttribute($url)
    {
    	$this->attributes['url'] = '/' . trim($url, '/');
    }
}
