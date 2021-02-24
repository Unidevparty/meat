<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Cache;

class Searchwords extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'short',
        'long'
    ];

    public static function getWords()
    {
        return Cache::rememberForever('short_search_words', function() {
            $words = Searchwords::all()->pluck('long', 'short')->toArray();

            $result = [];
            foreach ($words as $key => $value) {
                $result[mb_strtolower($key)] = mb_strtolower($value);
            }

            return $result;
        });
    }
}
