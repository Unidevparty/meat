<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Settings;

class Forum extends Model
{
	public static function topics($count = 6)
    {
    	return Cache::get('forum.topics', function () use ($count) {
			//$topics = IPB::api('forums/topics', ['sortBy' => 'date', 'sortDir' => 'desc']);

            $topics = [];

            $ids = explode(',', trim(Settings::getByKey('forums_topics'), ','));

            foreach ($ids as $id) {
                $id = trim($id);
				$topic = IPB::api('forums/topics/' . $id);
				$topic['posts'] = $topic['posts'] - 1;
                $topics[] = $topic;
            }


            // $t = [];

            // foreach ($topics['results'] as $topic) {
            //     if ($topic['hidden'] != 1) {
            //         $t[] =  $topic;
            //     }
            // }

    		$topics = array_slice($topics, 0, $count);

    		Cache::add('forum.topics', $topics, 60);

    		return $topics;
		}, 60);
    }

}
