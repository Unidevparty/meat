<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Meta extends Model
{
    protected $fillable = [
    	'url',
    	'name',
    	'title',
    	'keywords',
    	'description'
    ];


}
