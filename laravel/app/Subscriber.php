<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    protected $fillable = [
    	'email',
    	'name',
        'confirmed',
        'confirm_code',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->generateConfirmCode();
        });
    }

    protected function generateConfirmCode() {
        $this->attributes['confirm_code'] = md5($this->attributes['email']);
    }
}
