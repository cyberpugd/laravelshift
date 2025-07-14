<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChangeApproval extends BaseModel
{
    protected $fillable = [
          'approved', 'date_approved', 'approver', 'approval_type'
    ];
    protected $dates = [
          'date_approved'
    ];

    public function changeTicket()
    {
         return $this->belongsTo(\App\ChangeTicket::class);
    }

    public function approvedBy()
    {
         return $this->belongsTo(\App\User::class, 'approver');
    }
}
