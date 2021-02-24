<?php

namespace App;

use Carbon\Carbon;
use App\SearchIndex;
use Illuminate\Database\Eloquent\Model;
use App\NewsTag;
use App\Comment;
use Illuminate\Http\Request;

class News extends Model
{
    use SearchIndex;

    protected $searchattributes = [
        'name',
		'text',
		'introtext',
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
		'on_main',
		'text',
		'introtext',
		'wiews',
		'textru_uid',
		'published_at',
		'published',
		'textru',
        'source',
        'company_id',
        'email_sent_to_manager',
	];

	protected $attributes = [
        'published' => 0,
        'textru' => '',
        'textru_uid' => '',
        'source' => '',
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

	public function setSourceImageAttribute($image) {

		$old_image = app_path() . '/../..' . $this->source_image;
		if ($this->source_image && is_file($old_image)) {
			unlink($old_image);
		}

        $this->attributes['source_image'] = saveUploadedImage($image, $this->name);
	}

	public function setMainImageAttribute($image) {
		if (!$image) return;

		$old_image = app_path() . '/../..' . $this->main_image;
		if ($this->main_image && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_main_image';

		$this->attributes['main_image'] = saveCroppedImage($image, $image_name);
	}

	public function setPreviewAttribute($image) {
		if (!$image) return;

		$old_image = app_path() . '/../..' . $this->preview;
		if ($this->preview && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_preview';

		$this->attributes['preview'] = saveCroppedImage($image, $image_name);
	}

	public function setOnMainAttribute($image) {
		if (!$image) return;

		$old_image = app_path() . '/../..' . $this->on_main;
		if ($this->on_main && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_on_main';

		$this->attributes['on_main'] = saveCroppedImage($image, $image_name);
	}

	public function tags() {
		return $this->belongsToMany(NewsTag::class, 'news_tags');
	}

	public function updateTags($tags) {
		$this->tags()->detach();

		if (!$tags) {
			return $this;
		}

		foreach ($tags as $tag) {
			$tag = NewsTag::firstOrNew(['name' => $tag]);
			$tag->save();

			$this->tags()->attach($tag);
		}

		return $this;
	}


	public function getCommentsCountAttribute()
	{
		return Comment::where('type', 'news')->where('page_id', $this->id)->count();
	}

	public function getCommentsAttribute()
	{
		return Comment::where('type', 'news')->where('page_id', $this->id)->get();
	}

	public function scopeLatest($query) {
		return $query->orderBy('published_at', 'desc');
	}

	public function scopePublished($query) {
		return $query->where('published_at', '<=', Carbon::now())->where('published', 1)->latest();
    }

    function getTextruDataAttribute() {
    	return unserialize($this->textru);
    }




    public function getTagnamesAttribute()
    {
        return implode(', ', $this->tags()->pluck('name')->toArray());
    }

    public function getCompanynameAttribute()
    {
        return Company::find($this->company_id)->name;
    }
}
