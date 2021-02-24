<?php

namespace App;

use App\IPB;
use Illuminate\Support\Facades\Cache;

class Blog
{

    public static function posts($count = 12)
    {
    	return Cache::get('blog.posts', function () use ($count) {
			$posts_res = IPB::api('/blog/entries', ['sortBy' => 'date', 'sortDir' => 'desc']);

			$posts = [];

			foreach ($posts_res['results'] as $post) {
				if ($post['hidden']) continue;

				$posts[] = $post;
			}

			$posts = array_slice($posts, 0, $count);


			Cache::add('blog.posts', $posts, 60);

			return $posts;
		}, 60);
    }

    public static function bloggers($count = 5)
    {
    	return Cache::get('blog.bloggers', function () use ($count) {
			$posts = IPB::api('/blog/entries', ['sortBy' => 'date', 'sortDir' => 'desc']);

	    	$bloggers = [];

	    	foreach ($posts['results'] as $post) {

	    		if (!isset($bloggers[$post['author']['id']])) {
	    			$bloggers[$post['author']['id']] = $post['author'];
	    		}
	    	}

	    	$bloggers = array_slice($bloggers, 0, $count);


	    	Cache::add('blog.bloggers', $bloggers, 60);

			return $bloggers;
		}, 60);

    }
}
