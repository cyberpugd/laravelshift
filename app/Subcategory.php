<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcategory extends BaseModel
{
     protected $fillable = ['category_id', 'name', 'description', 'tags',  'location_matters', 'created_by', 'active'];
    public function category() {
          return $this->belongsTo(\App\Category::class);
    }

    public function tickets() {
          return $this->hasMany(\App\ticket::class, 'sub_category_id');
    }

    public function identifiableName()
    {
        return $this->name;
    }
    public function createdBy()
    {
          return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function teams()
    {
         return $this->belongsToMany(\App\Team::class);
    }

    public function syncTeams($teams)
    {
         return $this->teams()->sync($teams);
    }

    public function ticketCount()
    {
         return $this->tickets()->count();
    }

    public function getActiveAttribute($value)
    {
         return (bool)$value;
    }

    public function getLocationMattersAttribute($value)
    {
         return (bool)$value;
    }
}
