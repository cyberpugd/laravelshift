<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FormColumn extends BaseModel
{
    protected $fillable = [
        'form_id',
        'name',
        'label',
        'type',
        'is_required',
        'ticket_subject',
        'ticket_description',
        'default_value'
    ];

    public function form()
    {
        return $this->belongsTo('App\Form');
    }

    public function getIsRequiredAttribute($value)
    {
         return (bool) $value;
    }

    public function getTicketSubjectAttribute($value)
    {
         return (bool) $value;
    }

    public function getTicketDescriptionAttribute($value)
    {
         return (bool) $value;
    }

    public function getDefaultValueAttribute($value)
    {
         if($this->type == 'checkbox') {
               return (bool)(Integer)$value;    
         }
         return $value;
    }
}
