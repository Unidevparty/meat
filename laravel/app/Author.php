<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Interview;
use App\Article;
use App\Company;

class Author extends Model
{
    protected $fillable = [
        'name',
        'post',
        'company_id',
        'photo'
    ];

    public function company() {
        return $this->belongsTo(Company::class);
    }

    public function articles() {
        return $this->belongsToMany(Article::class, 'article_author');
    }

    public function interviews() {
        return $this->belongsToMany(Interview::class, 'author_interview');
    }
}
