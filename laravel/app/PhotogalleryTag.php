<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Photogallery;

class PhotogalleryTag extends Model
{
    protected $table = 'tags_photogalleries';

	protected $fillable = [
		'name',
		'alias'
	];

	public function setNameAttribute($name)
	{
		$this->attributes['name'] = $name;
		$this->attributes['alias'] = str_slug($name);
	}

	public function photogalleries()
	{
		return $this->belongsToMany(Photogallery::class, 'photogalleries_tags');
	}
}
