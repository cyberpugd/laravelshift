<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Announcement extends BaseModel
{
    protected $dates = ['start_date', 'end_date'];
    protected $fillable = ['type', 'title', 'details', 'start_date', 'end_date', 'location'];
}
