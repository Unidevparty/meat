<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

	public function roles()
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function getRolesAttribute() {
        return $this->roles()->pluck('id')->toArray();
    }

    /**
     * Проверяет есть ли у пользователя заданная роль
     * @param string $role_name
     * @return boolean
     */
    public function hasRole($role)
    {
        if (is_array($role)) {
            
            return (bool) $this->roles()->whereIn('name', $role)->first();
        }
        return (bool) $this->roles()->where('name', $role)->first();
    }
}
