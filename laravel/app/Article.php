<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\ArticleTag;
use App\Company;
use App\Search;
use App\SearchIndex;
use App\Author;

class Article extends Model
{
    use SearchIndex;

    protected $searchattributes = [
        'name',
        'title',
        'text',
        'introtext',
        'authornames',
        'tagnames',
        'companyname'
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
		'preview_big',
		'on_main',
		'big_on_main_slider',
		'sm_on_main_slider',
		'company',
		'text',
		'introtext',
		'views',
		'textru_uid',
		'published_at',
		'published',
		'for_registered',
        'textru',
        'email_sent_to_manager',
	];

	protected $attributes = [
        'published' => 0,
        'textru' => '',
        'textru_uid' => '',
    ];

	protected $dates = ['published_at'];

	public function setNameAttribute($name) {
		$this->attributes['name'] = $name;
		$this->attributes['alias'] = str_slug($this->id . '-' . $name);
	}

	public function setPublishedAtAttribute($published_at) 	{
		if (!$published_at) {
			$this->attributes['published_at'] = Carbon::now();
		} else {
			$this->attributes['published_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $published_at);
		}
	}

    public function company() {
    	return $this->belongsTo(Company::class);
    }

	public function authors() {
    	return $this->belongsToMany(Author::class, 'article_author');
    }

    public function tags() {
		return $this->belongsToMany(ArticleTag::class, 'articles_tags');
	}

	public function updateTags($tags) {
		$this->tags()->detach();

		if (!$tags) {
			return $this;
		}

		foreach ($tags as $tag) {
			$tag = ArticleTag::firstOrNew(['name' => $tag]);
			$tag->save();

			$this->tags()->attach($tag);
		}

		return $this;
	}


	public function getCommentsCountAttribute()
	{
		return Comment::where('type', 'articles')->where('page_id', $this->id)->count();
	}

	public function getCommentsAttribute()
	{
		return Comment::where('type', 'articles')->where('page_id', $this->id)->get();
	}

	public function scopeLatest($query) {
		return $query->orderBy('published_at', 'desc');
	}

	public function scopePublished($query) {
		return $query->where('published_at', '<=', Carbon::now())->where('published', 1)->latest();
    }

    public function scopeMostViewed($query) {
    	return $query->where('published', 1)->where('published_at', '>=', Carbon::now()->subDays(14))->orderBy('views', 'desc')->take(5)->get();
    }



    // Предыдущая статья
    public function getPreviousAttribute()
    {
    	return Article::where('published_at', '<=', Carbon::now())
    				->where('published', 1)
    				->where('published_at', '<', $this->published_at)
    				->orderBy('published_at','desc')
    				->first();
    }

   	// Cледующая статья
    public function getNextAttribute()
    {
    	return Article::where('published_at', '<=', Carbon::now())
    				->where('published', 1)
    				->where('published_at', '>', $this->published_at)
    				->orderBy('published_at','asc')
    				->first();
    }








 //    public function setAuthorImgAttribute($image) {

	// 	$old_image = app_path() . '/../..' . $this->author_img;
	// 	if ($this->author_img && is_file($old_image)) {
	// 		unlink($old_image);
	// 	}

 //        $image = build_file_name('author' . $this->name) . $image->getClientOriginalExtension();

 //        request()->file('author_img')->move(app_path() . '/../../uploads/', $image);

	// 	$this->attributes['author_img'] = '/uploads/' . $image;
	// }

    public function setSourceImageAttribute($image) {
		if (!$image) {
			$this->attributes['source_image'] = $image;
		}
		// $old_image = $this->source_image;
		if ($this->source_image && is_file($this->source_image)) {
			unlink($this->source_image);
		}

		$this->attributes['source_image'] = saveUploadedImage($image, $this->name);
	}

	public function setMainImageAttribute($image) {
		if (!$image) {
			$this->attributes['main_image'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->main_image;
		if ($this->main_image && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_main_image';

		$this->attributes['main_image'] = saveCroppedImage($image, $image_name);
	}

	public function setPreviewAttribute($image) {
		if (!$image) {
			$this->attributes['preview'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->preview;
		if ($this->preview && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_preview';

		$this->attributes['preview'] = saveCroppedImage($image, $image_name);
	}

	public function setPreviewBigAttribute($image) {
		if (!$image) {
			$this->attributes['preview_big'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->preview_big;
		if ($this->preview_big && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_preview_big';

		$this->attributes['preview_big'] = saveCroppedImage($image, $image_name);
	}

	public function setOnMainAttribute($image) {
		if (!$image) {
			$this->attributes['on_main'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->on_main;
		if ($this->on_main && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_on_main';

		$this->attributes['on_main'] = saveCroppedImage($image, $image_name);
	}



	public function setBigOnMainSliderAttribute($image) {
		if (!$image) {
			$this->attributes['big_on_main_slider'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->big_on_main_slider;
		if ($this->big_on_main_slider && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_big_on_main_slider';

		$this->attributes['big_on_main_slider'] = saveCroppedImage($image, $image_name);
	}
	public function setSmOnMainSliderAttribute($image) {
		if (!$image) {
			$this->attributes['sm_on_main_slider'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->sm_on_main_slider;
		if ($this->sm_on_main_slider && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_sm_on_main_slider';

		$this->attributes['sm_on_main_slider'] = saveCroppedImage($image, $image_name);
	}

	function getTextruDataAttribute() {
    	return unserialize($this->textru);
    }





    public function getAuthornamesAttribute()
    {
        return implode(', ', $this->authors()->pluck('name')->toArray());
    }

    public function getTagnamesAttribute()
    {
        return implode(', ', $this->tags()->pluck('name')->toArray());
    }

    public function getCompanynameAttribute()
    {
        return Company::find($this->company)->name;
    }
}
