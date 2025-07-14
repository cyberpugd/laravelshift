<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends BaseModel
{
     protected $fillable = ['name', 'label'];
    public function permissions()
    {
         return $this->belongsToMany('App\Permission');
    }

    public function givePermissionTo(Permission $permission)
    {
         return $this->permissions()->save($permission);
    }

    public function syncPermissions($permissions)
    {
         return $this->permissions()->sync($permissions);
    }

     public function hasPermission($permission)
    {
         if(is_string($permission)) {
               return $this->permissions->contains('name', $permission);
         }
         return !! $permission->intersect($this->permissions)->count();
    }
}
