<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;

class Team extends Model
{
    protected $table = 'teams';
    protected $fillable = ['name', 'self_enroll'];
    protected $hidden = ['pivot'];

    public function subcategories()
    {
         return $this->belongsToMany('App\Subcategory');
    }

     public function subcategoriesOrdered() {
          return $this->belongsToMany('App\Subcategory')->where('active', 1)->orderBy('name');
     }

    public function users()
    {
         return $this->belongsToMany('App\User')->orderBy('first_name');
    }

    public function syncSubcategories($subcategories)
    {
         return $this->subcategories()->sync($subcategories);
    }

     public function syncUsers($users)
    {
         return $this->users()->sync($users);
    }

    public function getSelfEnrollAttribute($value)
    {
         return (bool)$value;
    }

     public function getCreatedAtAttribute($value)
    {
         return Carbon::createFromFormat('Y-m-d H:i:s.u0', $value, 'UTC')->tz(Auth::user()->timezone);
    }
}
