<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Article;

class ArticleTag extends Model
{
    protected $table = 'tags_articles';

	protected $fillable = [
		'name',
		'alias'
	];

	public function setNameAttribute($name)
	{
		$this->attributes['name'] = $name;
		$this->attributes['alias'] = str_slug($name);
	}

	public function articles()
	{
		return $this->belongsToMany(Article::class, 'articles_tags');
	}
}
