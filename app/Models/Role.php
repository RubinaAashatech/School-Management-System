<?php

namespace App\Models;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    const ADMINISTRATOR = 1;
    const USER = 2;

    protected $fillable = ['name', 'display_name', 'description'];

//    public function permissions()
//    {
//        return $this->hasMany('App\Models\Permission');
//    }

    public function getRequestPath()
    {
        if ($this->id == self::ADMINISTRATOR) {
            return 'admin';
        } elseif ($this->id == self::USER) {
            return 'user';
        }

        return null;
    }

    public function permissions(){
        return $this->belongsToMany(Permission::class);
     }
}