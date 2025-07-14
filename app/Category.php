<?php

namespace App;
use Carbon\Carbon;
use Auth;
use Illuminate\Database\Eloquent\Model;

class Category extends BaseModel
{
     protected $fillable = ['name'];
     
     public function subcategories() {
          return $this->hasMany('App\Subcategory', 'category_id');
     }

     public function subcategoriesOrdered() {
          return $this->hasMany('App\Subcategory', 'category_id')->where('active', 1)->orderBy('name');
     }

     public function tickets() {
          return $this->hasMany('App\ticket', 'category_id');
     }

     public function identifiableName()
    {
        return $this->name;
    }

    public function getActiveAttribute($value)
    {
         return (bool)$value;
    }

    public function getCreatedAtAttribute($value)
    {
         return Carbon::createFromFormat('Y-m-d H:i:s.u0', $value, 'UTC')->tz(Auth::user()->timezone);
    }
}
