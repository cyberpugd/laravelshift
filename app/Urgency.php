<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Urgency extends Model
{
    protected $table = 'urgency';

    public function tickets() {
          return $this->hasMany('App\ticket', 'urgency_id');
    }

    public function identifiableName()
    {
        return $this->name;
    }
}
