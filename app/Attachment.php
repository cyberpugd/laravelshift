<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attachment extends BaseModel
{
    protected $fillable = [
    	'ticket_id',
    	'file',
    	'file_name',
    	'file_size'
    ];

    public function ticketable()
    {
          return $this->morphTo();
    }


}
