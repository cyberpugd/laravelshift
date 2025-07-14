<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeTicketTemplate extends Model
{
    protected $guarded = [];

    protected $dates = ['start_date', 'end_date'];

    public function sharedWith()
    {
         return $this->belongsToMany(\App\User::class);
    }

    public function owner()
    {
         return $this->belongsTo(\App\User::class, 'owner_id');
    }

    public function setStartDateAttribute($value)
    {
          if($value == '') {
               $this->attributes['start_date'] = null;
          } else {
               $this->attributes['start_date'] = $value;
          }
    }

    public function setEndDateAttribute($value)
    {
          if($value == '') {
               $this->attributes['end_date'] = null;
          } else {
               $this->attributes['end_date'] = $value;
          }
    }
}
