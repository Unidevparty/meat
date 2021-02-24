<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SearchIndex;

class Event extends Model
{
    use SearchIndex;

    protected $searchattributes = [
        'name',
        'title',
        'introtext',
        'text',
    ];

    protected $fillable = [
        'name',
        'alias',
        'title',
        'description',
        'keywords',
        'source_image',
        'main_image',
        'preview',
        'introtext',
        'text',
        'views',
        'published_at',
        'published',
    ];

	protected $attributes = [
        'published' => 0,
    ];

	protected $dates = ['published_at'];
}
