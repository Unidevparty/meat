<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Interview;

class InterviewTag extends Model
{
    protected $table = 'tags_interviews';

	protected $fillable = [
		'name',
		'alias'
	];

	public function setNameAttribute($name)
	{
		$this->attributes['name'] = $name;
		$this->attributes['alias'] = str_slug($name);
	}

	public function interviews()
	{
		return $this->belongsToMany(Interview::class, 'interviews_tags');
	}
}
