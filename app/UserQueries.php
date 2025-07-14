<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserQueries extends Model
{
     protected $table = 'user_queries';
    protected $fillable = ['columns', 'where_clause', 'query_type', 'name', 'user_id', 'sort_by', 'sort_direction'];

    public function user()
    {
         return $this->belongsTo(\App\User::class);
    }
}
