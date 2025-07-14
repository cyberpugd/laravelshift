<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserView extends Model
{
     protected $fillable = [
            'query_type',
            'query',
               'name',
               'select_columns',
               'has_filter',
               'where_columns',
     ];
    public function user()
    {
         return $this->belongsTo(\App\User::class);
    }

    public function filters()
    {
         return $this->hasMany(\App\FilterCriteria::class, 'view_id');
    }
}
