<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\SearchIndex;

use App\InterviewTag;
use App\Company;
use App\Author;

class Interview extends Model
{
    use SearchIndex;

    protected $searchattributes = [
        'name',
		'fio',
		'post',
		'introtext',
		'quote',
		'text',
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
		'fio',
		'post', // Должность
		'company_id',
		'introtext',
		'quote',
		'text',
		'views',
		'plagiarism',
		'published_at',
		'published',
		'for_registered',
		'textru',

        'main_slider',
        'main_slider_position',
        'main_slider_source_img',
        'main_slider_big_img',
        'main_slider_sm_img',
        'email_sent_to_manager',
	];

	protected $attributes = [
        'published' => 0,
        'main_slider' => 0,
        'name' => '',
        'textru' => '',
        'textru_uid' => '',
    ];

	protected $dates = ['published_at'];

	public function company() {
    	return $this->belongsTo(Company::class);
    }

    public function authors() {
    	return $this->belongsToMany(Author::class, 'author_interview');
    }

	public function setFioAttribute($fio) {
		$this->attributes['fio'] = $fio;
		$this->attributes['name'] = $fio;
		$this->attributes['alias'] = str_slug($this->id . '-' . $fio);
	}
	public function setPublishedAtAttribute($published_at) 	{
		if (!$published_at) {
			$this->attributes['published_at'] = Carbon::now();
		} else {
			$this->attributes['published_at'] = Carbon::createFromFormat('Y-m-d H:i:s', $published_at);
		}
	}

	public function setSourceImageAttribute($image) {
        if (!$image) {
			$this->attributes['source_image'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->source_image;
		if ($this->source_image && is_file($old_image)) {
			unlink($old_image);
		}

		$this->attributes['source_image'] = saveUploadedImage($image, $this->name);
	}

	public function setMainImageAttribute($image) {
        if (!$image) {
			$this->attributes['main_image'] = $image;
		}

		$image_name = build_file_name($this->name) . '_main_image';
		$this->attributes['main_image'] = save_image($image, $this->main_image, $image_name);
	}

	public function setPreviewAttribute($image) {
        if (!$image) {
			$this->attributes['preview'] = $image;
		}

		$image_name = build_file_name($this->name) . '_preview';
		$this->attributes['preview'] = save_image($image, $this->preview, $image_name);
	}

	public function tags() {
		return $this->belongsToMany(InterviewTag::class, 'interviews_tags');
	}

	public function updateTags($tags) {
		$this->tags()->detach();

		if (!$tags) {
			return $this;
		}

		foreach ($tags as $tag) {
			$tag = InterviewTag::firstOrNew(['name' => $tag]);
			$tag->save();

			$this->tags()->attach($tag);
		}

		return $this;
	}


	public function getCommentsCountAttribute()
	{
		return Comment::where('type', 'interviews')->where('page_id', $this->id)->count();
	}

	public function getCommentsAttribute()
	{
		return Comment::where('type', 'interviews')->where('page_id', $this->id)->get();
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


    public function setMainSliderSourceImgAttribute($image) {
        if (!$image) {
            $this->attributes['main_slider_source_img'] = $image;
        }

        $old_image = app_path() . '/../..' . $this->main_slider_source_img;
        if ($this->main_slider_source_img && is_file($old_image)) {
            unlink($old_image);
        }

        $this->attributes['main_slider_source_img'] = saveUploadedImage($image, $this->name . '_onm');
    }

    public function setMainSliderBigImgAttribute($image) {
        if (!$image) {
            $this->attributes['main_slider_big_img'] = $image;
        }

        $image_name = build_file_name($this->name) . '_main_slider_big_img';
        $this->attributes['main_slider_big_img'] = save_image($image, $this->main_slider_big_img, $image_name);
    }

    public function setMainSliderSmImgAttribute($image) {
        if (!$image) {
            $this->attributes['main_slider_sm_img'] = $image;
        }

        $image_name = build_file_name($this->name) . '_main_slider_sm_img';
        $this->attributes['main_slider_sm_img'] = save_image($image, $this->main_slider_sm_img, $image_name);
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
        return $this->company->name;
    }
}
