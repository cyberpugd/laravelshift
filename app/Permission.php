<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends BaseModel
{
    public function roles()
    {
         return $this->belongsToMany('App\Role');
    }
}
