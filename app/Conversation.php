<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Conversation extends BaseModel
{
     protected $fillable = [
          'ticket_id',
          'created_by',
          'source',
          'message'
     ];
     
    public function ticket()
    {
         return $this->belongsTo('App\Ticket');
    }
}
