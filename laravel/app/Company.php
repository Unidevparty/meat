<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\SearchIndex;

use App\News;
use App\Article;
use App\Interview;
use App\CompanyType;
use App\CompanyReview;

class Company extends Model
{
    use SearchIndex;

    protected $searchattributes = [
        'name',
        'type',
		'year',
        'title',
        'introtext',
        'text',
        'email',
        'site',
        'facebook',
        'google_plus',
        'vk',
        'instagram',
        'address',
        'country',
        'region',
        'city',
    ];

    protected $fillable = [
    	'name',
        'alias',
    	'logo',
		'type',
		'year',
        'title',
        'description',
        'keywords',
        'introtext',
        'text',
        'contacts',
        'email',
        'email_checked',
        'email_check_fails',
        'gallery',
        'videos',
        'files',
        'published_at',
        'published',
        'views',
        'holding_id',
        'is_holding',
        'site',
        'facebook',
        'google_plus',
        'vk',
        'instagram',
        'phone',
        'address',
        'coords',
        'country',
        'region',
        'city',
        'brands',
        'rating',
        'supplier',
        'manufacturer',
        'manager_email',
    ];

	protected $attributes = [
		'logo' => '',
        'views' => 0,
        'type' => '',
	];

    protected $dates = [
        'published_at',
        'year'
    ];

    public function holding()
    {
        return $this->belongsTo(Company::class, 'holding_id');
    }

    public function holding_companies()
    {
        return $this->hasMany(Company::class, 'holding_id');
    }


    public function articles()
    {
    	return $this->hasMany(Article::class, 'company');
    }

    public function news()
    {
    	return $this->hasMany(News::class);
    }

    public function interviews()
    {
    	return $this->hasMany(Interview::class);
    }

    public function jobs()
    {
    	return $this->hasMany(Job::class);
    }

    public function types() {
        return $this->belongsToMany(CompanyType::class);
    }


    public function reviews()
    {
        return $this->hasMany(CompanyReview::class);
    }
    public function updateRating()
    {
        $reviews = $this->reviews;
        $count = 0;
        $val = 0;

        foreach ($reviews as $review) {
            $count++;
            $val += $review->rate;
        }

        $this->rating = $val / $count;
        $this->save();
    }

    public function updateTypes($types) {
        $this->types()->sync($types);

        // if (!$types) {
        //     return $this;
        // }
        //
        // foreach ($types as $type) {
        //     $type = CompanyType::find($type);
        //     //$type->save();
        //
        //     $this->types()->attach($type);
        // }
        //
        return $this;
    }


    public function setNameAttribute($name) {
        $this->attributes['name'] = $name;
        $this->attributes['alias'] = str_slug($this->id . '-' . $name);
    }

    // public function setGalleryAttribute($gallery) {
    //     foreach ($gallery as $key => $g) {
	// 		if (!$g['photo']) unset($gallery[$key]);
	// 	}
    //
    //     $this->attributes['gallery'] = json_encode(array_values($gallery));
    // }

    public function getGalleryAttribute() {
        return json_decode($this->attributes['gallery']);
    }

    public function setBrandsAttribute($brands) {
        foreach ($brands as $key => $g) {
			if (!$g['photo']) unset($brands[$key]);
		}

        $this->attributes['brands'] = json_encode(array_values($brands));
    }

    public function getBrandsAttribute() {
        return json_decode($this->attributes['brands']);
    }

    /**
     * Все торговые марки холдинга
     */
    public function getAllBrandsAttribute()
    {
        if (!$this->is_holding) {
            return $this->brands;
        }
        $brands = collect($this->brands);

        foreach ($this->holding_companies as $company) {
            $brands = $brands->merge($company->brands);
        }

        $brands = $brands->unique(function ($item) {
            return $item->photo . $item->name;
        });

        return $brands;
    }

    public function setVideosAttribute($videos) {
        foreach ($videos as $key => $v) {
			if (!$v['url']) {
                unset($videos[$key]);

                continue;
            }

            if (!$v['photo']) {
                $videos[$key]['photo'] = getYoutubeImage($v['url']);
            }
		}



        $this->attributes['videos'] = json_encode(array_values($videos));
    }

    public function getVideosAttribute() {
        return json_decode($this->attributes['videos']);
    }

    public function getYearFormattedAttribute()
    {
        if (substr($this->attributes['year'], 5) == '01-01 00:00:00') {
            return substr($this->attributes['year'], 0, 4);
        }

        if (!$this->year) return '';

        return \LocalizedCarbon::instance($this->year)->formatLocalized('%d&nbsp;%f&nbsp;%Y');
    }



    public function setFilesAttribute($files) {
        foreach ($files as $key => $f) {
			if (!$f['file']) {
                unset($files[$key]);
            }
            else {
                $filetime = filemtime(trim($f['file'], '/'));

                $files[$key]['ext']  = pathinfo($file->file, PATHINFO_EXTENSION);
                $files[$key]['size'] = str_file_size($f['file']);
                $files[$key]['date'] = \LocalizedCarbon::createFromTimestamp($filetime)
                                                    ->formatLocalized('%d %f ‘%y');
            }
		}

        $this->attributes['files'] = json_encode(array_values($files));
    }

    public function getFilesAttribute() {
        return json_decode($this->attributes['files']);
    }

	public function setLogoAttribute($image) {
		if ($this->logo && is_file($this->logo)) {
			unlink($this->logo);
		}

		$this->attributes['logo'] = saveUploadedImage($image, $this->name);
	}



    public function scopeLatest($query) {
        return $query->orderBy('published_at', 'desc');
    }

    public function scopePublished($query) {
        return $query->where('published_at', '<=', Carbon::now())->where('published', 1);
    }

    /**
     * Компания проверена
     */
    function getIsCheckedAttribute()
    {
        return $this->published_at->diffInDays(Carbon::now()) > 180; // Если опубликован дольше чем пол года назад то проверен
    }

    public static function countries_map($published = true)
    {
        $companies = null;

        if ($published) {
            $companies = Company::published()->get();
        } else {
            $companies = Company::all();
        }

        $countries_map = [];

        foreach ($companies as $company) {
            if (
                isset($countries_map[$company->country])
                && isset($countries_map[$company->country][$company->region])
                && isset($countries_map[$company->country][$company->region][$company->city])
            ) {
                $countries_map[$company->country][$company->region][$company->city]++;
            } else {
                $countries_map[$company->country][$company->region][$company->city] = 1;
            }
        }

        return $countries_map;
    }



    public function setPhoneAttribute($value)
    {
        $data = [];

        foreach ($value['number'] as $key => $phone) {
            if (!$phone && !$value['description'][$key]) continue;

            $data[] = [
                'number' => $phone,
                'description' => $value['description'][$key]
            ];
        }

        $this->attributes['phone'] = json_encode($data);
    }

    public function getPhoneAttribute()
    {
        return json_decode($this->attributes['phone']);
    }

    function savePhotos($request) {
        $new_photos = $request->get('new_photos');
        $new_photo_files = $request->file('new_photos');

        $photos = [];

        foreach ($new_photos as $id => $photo) {
            $photo_image = saveUploadedImage($new_photo_files[$id]['file'], $this->name);

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
        }, $this->gallery);

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

        $this->gallery = json_encode(array_values($photos));

        $this->save();
    }
}
