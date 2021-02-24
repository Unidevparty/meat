<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\PhotogalleryTag;
use Carbon\Carbon;

class Photogallery extends Model
{
    protected $fillable = [
        'name',
        'alias',
        'title',
        'description',
        'keywords',
        'image',
        'introtext',
        'text',
        //'photos',
        'published_at',
        'published',
        'views',
        'company_id',
        'event_id',
        'main_image',
        'category_image',
        'home_image_1',
        'home_image_2',
    ];

	protected $attributes = [
        'views' => 0
	];

    protected $dates = [
        'published_at'
    ];

    public function tags() {
		return $this->belongsToMany(PhotogalleryTag::class, 'photogalleries_tags');
	}

    public function setNameAttribute($name) {
        $this->attributes['name'] = $name;
        $this->attributes['alias'] = str_slug($this->id . '-' . $name);
    }

    // public function setPhotosAttribute($photos) {
    //     foreach ($photos as $key => $photo) {
    //         if (!$photo['photo']) unset($photos[$key]);
    //     }
    //
    //     $this->attributes['photos'] = json_encode(array_values($photos));
    // }

    function savePhotos($request) {
        $new_photos = $request->get('new_photos');
        $new_photo_files = $request->file('new_photos');

        $photos = [];

        foreach ($new_photos as $id => $photo) {
            $photo_image = saveUploadedImage($new_photo_files[$id]['file'], $photogallery->name);

            if ($photo) {
                $photos[$photo['sort']] = [
                    'id' => $id,
                    'photo' => $photo_image,
                    'description' => $photo['description']
                ];
            }
        }

        $old_photos = $request->get('photos');

        foreach ($old_photos as $id => $photo) {
            if ($photo['photo']) {
                $photos[$photo['sort']] = [
                    'id' => $id,
                    'photo' => $photo['photo'],
                    'description' => $photo['description']
                ];
            }
        }

        ksort($photos);

        // Удаляем удаленные фотки с сервера
        $old_photos = array_map(function($photo) {
            return $photo->photo;
        }, $this->photos);

        $new_photos = array_map(function($photo) {
            return $photo['photo'];
        }, $photos);

        $del = array_diff($old_photos, $new_photos);

        foreach ($del as $photo) {
            $photo = trim($photo, '/');
            if (is_file($photo)) {
                unlink($photo);
            }
        }

        $this->photos = json_encode(array_values($photos));

        $this->save();
    }

    public function getPhotosAttribute() {
        return json_decode($this->attributes['photos']);
    }

	public function updateTags($tags) {
		$this->tags()->detach();

		if (!$tags) {
			return $this;
		}

		foreach ($tags as $tag) {
			$tag = PhotogalleryTag::firstOrNew(['name' => $tag]);
			$tag->save();

			$this->tags()->attach($tag);
		}

		return $this;
	}

    // Сохраняем картинки
    public function setImageAttribute($image) {
		if (!$image) {
			$this->attributes['image'] = $image;
		}

		if ($this->image && is_file($this->image)) {
			unlink($this->image);
		}

		$this->attributes['image'] = saveUploadedImage($image, $this->name);
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


    public function setCategoryImageAttribute($image) {
		if (!$image) {
			$this->attributes['category_image'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->category_image;
		if ($this->category_image && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_category_image';

		$this->attributes['category_image'] = saveCroppedImage($image, $image_name);
	}


    public function setHomeImage1Attribute($image) {
		if (!$image) {
			$this->attributes['home_image_1'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->home_image_1;
		if ($this->home_image_1 && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_home_image_1';

		$this->attributes['home_image_1'] = saveCroppedImage($image, $image_name);
	}

    public function setHomeImage2Attribute($image) {
		if (!$image) {
			$this->attributes['home_image_2'] = $image;
		}

		$old_image = app_path() . '/../..' . $this->home_image_2;
		if ($this->home_image_2 && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_home_image_2';

		$this->attributes['home_image_2'] = saveCroppedImage($image, $image_name);
	}

    // Выбор опубликованных
    public function scopeLatest($query) {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopePublished($query) {
        return $query->where('published_at', '<=', Carbon::now())->where('published', 1)->latest();
    }

    // Комментарии
    public function getCommentsCountAttribute()
    {
        return Comment::where('type', 'photogallery')->where('page_id', $this->id)->count();
    }

    public function getCommentsAttribute()
    {
        return Comment::where('type', 'photogallery')->where('page_id', $this->id)->get();
    }
}
