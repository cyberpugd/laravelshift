<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AuditUnit extends BaseModel
{
    protected $fillable = ['name', 'status'];
}
