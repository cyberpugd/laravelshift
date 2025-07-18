<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revision extends BaseModel
{
    protected $fillable = ['revisionable_type', 'revisionable_id', 'user_id', 'key', 'old_value', 'new_value'];
    
}
