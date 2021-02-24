<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Banner extends Model
{
	const POSITIONS = [
		'A-1' => 'A-1',
		'A-2' => 'A-2',
		'A-3' => 'A-3',
		//'A-4' => 'A-4',
		'B-1' => 'B-1',
		'B-2' => 'B-2',
		'C-1' => 'C-1',
		'C-2' => 'C-2',
		'H-1' => 'H-1',
		'K'   => 'K',
		'P-1' => 'P-1',
		'T-1' => 'T-1',
		'T-2' => 'T-2',
		'X-1' => 'X-1',
		'X-2' => 'X-2',
		'articles' => 'Баннер в статьях',
		'news' => 'Баннер в новостях',
		'interview' => 'Баннер в интервью',
		'partner' => 'Партнерский баннер',

		'mail_1' => 'Баннер в письме 1',
		'mail_2' => 'Баннер в письме 2',
	];

    protected $fillable = [
		'name',
		'start_date',
		'end_date',
		'number',
		'url',
		'main_image',
		'tablet_image',
		'mobile_image',
		'position',
		'views',
		'clicks',
		'published',
		'bydefault',
	];

	protected $attributes = [
        'published' => 0,
        'bydefault' => 0,
        'views' => 0,
        'clicks' => 0,
        'mobile_image' => '',
        'tablet_image' => '',
    ];

	protected $dates = [
		'start_date',
		'end_date',
	];

	// public function setPositionAttribute($position) {
	// 	$this->attributes['position'] = self::POSITIONS[$position];
	// }

	public function setMainImageAttribute($img) {
		$old_image = app_path() . '/../..' . $this->main_image;
		if ($this->main_image && is_file($old_image)) {
			unlink($old_image);
		}

        $image = build_file_name('mn_'.$this->name) . '.' . $img->getClientOriginalExtension();

        request()->file('main_image')->move(app_path() . '/../../uploads/banners/', $image);

		$this->attributes['main_image'] = '/uploads/banners/' . $image;
	}

	public function setMobileImageAttribute($img) {

		$old_image = app_path() . '/../..' . $this->mobile_image;
		if ($this->mobile_image && is_file($old_image)) {
			unlink($old_image);
		}

        $image = build_file_name('mb_' . $this->name) . '.' . $img->getClientOriginalExtension();

        request()->file('mobile_image')->move(app_path() . '/../../uploads/banners/', $image);

		$this->attributes['mobile_image'] = '/uploads/banners/' . $image;
	}

	public function setTabletImageAttribute($img) {

		$old_image = app_path() . '/../..' . $this->tablet_image;
		if ($this->tablet_image && is_file($old_image)) {
			unlink($old_image);
		}

        $image = build_file_name('mb_' . $this->name) . '.' . $img->getClientOriginalExtension();

        request()->file('tablet_image')->move(app_path() . '/../../uploads/banners/', $image);

		$this->attributes['tablet_image'] = '/uploads/banners/' . $image;
	}

	public function scopePublished($query) {
		return $query->where('start_date', '<=', Carbon::now())->where('end_date', '>=', Carbon::now())->where('published', 1);
    }

    public function getPositionNameAttribute()
	{
		return Banner::POSITIONS[$this->position];
	}

	public function getFakeUrlAttribute()
	{
		return route('banner.url', $this->id);
	}
}
