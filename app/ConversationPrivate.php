<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ConversationPrivate extends BaseModel
{
     protected $table= 'conversations_private';
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
