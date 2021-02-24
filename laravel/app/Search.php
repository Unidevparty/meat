<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Searchwords;

class Search extends Model
{
    use Searchable;

    protected $table = 'search';

    protected $fillable = [
        'searchindex',
        'searchable_id',
        'published_at',
        'name',
        'views',
        'searchable_type',
    ];

    protected $dates = [
        'published_at',
    ];

    public $timestamps = false;

    public function searchable()
    {
        return $this->morphTo();
    }

    public function toSearchableArray()
    {
        $data = $this->toArray();

        return [
            // 'id'            => $data['id'],
            // 'published_at'  => $data['published_at'],
            'name'          => $data['name'],
            // 'views'         => $data['views'],
            'searchindex'   => $data['searchindex'],
        ];
    }

    public static function customSearch($search_words)
    {
        return Search::where('name', 'like', '%' . $search_words .'%')->orWhere('searchindex', 'like', '%' . $search_words .'%');
    }



    public static function replace_search($search_words)
    {
        $short_words = Searchwords::getWords();

        $search_words = mb_strtolower($search_words);
        $search_words = explode(' ', $search_words);

        $need_search_words = [];

        foreach ($search_words as $word) {
            $need_search_words[] = $word;

            if (!empty($short_words[$word])) {
                $need_search_words[] = $short_words[$word];
            }
        }

        return implode(' ', $need_search_words);
    }
}
