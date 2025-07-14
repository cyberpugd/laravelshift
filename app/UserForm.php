<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Auth;

class UserForm extends BaseModel
{
    protected $fillable = [
        'name',
        'url',
        'subcategory_id',
        'active',
        'urgency',
        'slug',
        'ticket_subject',
        'ticket_description',
        'owner_id',
        'last_modified_by',
        'updated_at'
    ];

    public function fields()
    {
        return $this->hasMany('App\FormColumn', 'form_id');
    }

    public function users()
    {
          return $this->belongsToMany('App\User', 'user_user_form', 'form_id', 'user_id');
    }

    public function share_with()
    {
          return $this->belongsToMany('App\User', 'user_user_form', 'form_id', 'user_id');
    }

    public function owner()
    {
         return $this->belongsTo('App\User', 'owner_id');
    }

    public function last_modified()
    {
         return $this->belongsTo('App\User', 'last_modified_by');
    }

        public function getUpdatedAtAttribute($value)
    {
         return Carbon::createFromFormat('Y-m-d H:i:s.u0', $value, 'UTC')->tz(Auth::user()->timezone);
    }
}
