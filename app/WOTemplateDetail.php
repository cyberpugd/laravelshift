<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WOTemplateDetail extends Model
{
     protected $table = 'work_order_template_details';
    protected $fillable = [
          'template_id', 'assigned_to', 'subject', 'work_requested', 'due_in'
    ];
    public function template()
    {
         return $this->belongsTo('App\WorkOrderTemplate', 'template_id');
    }

    public function assignedTo()
    {
         return $this->belongsTo('App\User', 'assigned_to');
    }
}
