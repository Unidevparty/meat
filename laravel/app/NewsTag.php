<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\News;

class NewsTag extends Model
{
	protected $table = 'tags_news';

	protected $fillable = [
		'name',
		'alias'
	];

	public function setNameAttribute($name)
	{
		$this->attributes['name'] = $name;
		$this->attributes['alias'] = str_slug($name);
	}

	public function news()
	{
		return $this->belongsToMany(News::class, 'news_tags');
	}
}
