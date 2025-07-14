<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Holiday extends BaseModel
{
    protected $fillable = ['name', 'date'];
    protected $dates = ['date'];
    protected $hidden = ['pivot'];
    public function location()
    {
        return $this->belongsToMany(\App\Location::class);
    }
}
