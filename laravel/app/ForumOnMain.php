<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ForumOnMain extends Model
{
    protected $fillable = [
		'forum_id',
		'source_image',
		'image',
		'big_on_main_slider',
		'sm_on_main_slider',
		'name',
		'position'
	];

    public function setSourceImageAttribute($image) {

		$old_image = app_path() . '/../..' . $this->source_image;
		if ($this->source_image && is_file($old_image)) {
			unlink($old_image);
		}

        $this->attributes['source_image'] = saveUploadedImage($image, $this->name);
	}

	public function setImageAttribute($image) {
		if (!$image) return;

		$old_image = app_path() . '/../..' . $this->image;
		if ($this->image && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_image';

		$this->attributes['image'] = saveCroppedImage($image, $image_name);
	}

	public function setBigOnMainSliderAttribute($image) {
		if (!$image) return;

		$old_image = app_path() . '/../..' . $this->big_on_main_slider;
		if ($this->big_on_main_slider && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_big_on_main_slider';

		$this->attributes['big_on_main_slider'] = saveCroppedImage($image, $image_name);
	}
	public function setSmOnMainSliderAttribute($image) {
		if (!$image) return;

		$old_image = app_path() . '/../..' . $this->sm_on_main_slider;
		if ($this->sm_on_main_slider && is_file($old_image)) {
			unlink($old_image);
		}

		$image_name = build_file_name($this->name) . '_sm_on_main_slider';

		$this->attributes['sm_on_main_slider'] = saveCroppedImage($image, $image_name);
	}
}
