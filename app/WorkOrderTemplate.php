<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkOrderTemplate extends Model
{
     protected $fillable = ['name', 'owner_id'];
     protected $table = 'work_order_templates';

    public function user()
    {
         return $this->belongsTo('App\User', 'owner_id');
    }

     public function templateDetail()
    {
        return $this->hasMany('App\WOTemplateDetail', 'template_id');
    }

    public function users()
    {
        return $this->belongsToMany('App\User');
    }
}
