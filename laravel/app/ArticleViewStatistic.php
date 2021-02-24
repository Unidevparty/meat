<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleViewStatistic extends Model
{
	protected $fillable = [
        'url',
        'article_id',
        'category',
        'percent',
        'user_id',
        'group_id',
        'ip',
        'views',
    ];

    public function scopeIp($query, $ip)
    {
        return $query->where('ip', $ip);
    }
    
    public function scopeArticle($query, $id)
    {
        return $query->where('article_id', $id);
    }

    public function scopeCategory($query, $value)
    {
        return $query->where('category', $value);
    }
}
