<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends BaseModel
{
    protected $table = 'locations';
    protected $hidden = ['pivot'];
    protected $fillable = [
          'city',
          'timezone',
    ];

    public function users()
    {
        return $this->hasMany(\App\User::class);
    }

    public function holidays()
    {
        return $this->belongsToMany(\App\Holiday::class);
    }

    public function syncHolidays($holidays)
    {
        return $this->holidays()->sync($holidays);
    }

    public function hasHoliday($holiday)
    {
        return $this->holidays->contains('id', $holiday);
    }
}
