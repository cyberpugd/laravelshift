<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use \Venturecraft\Revisionable\RevisionableTrait;
use Illuminate\Database\Eloquent\Model;

class WorkOrder extends BaseModel
{
    use RevisionableTrait, Eloquence;

    protected $fillable = [
          'assigned_to',
          'created_by',
          'ticketable_id',
          'ticketable_type',
          'status',
          'subject',
          'work_requested',
          'work_completed',
          'completed_date',
          'due_date',
    ];

    protected $searchableColumns = [
          'status',
          'subject',
          'work_requested',
          'work_completed'
    ];

    protected $keepRevisionOf = [
        'status',
        'completed_date',
        'due_date'
    ];

    protected $revisionFormattedFieldNames = [
        'status' => 'Status',
        'completed_date' => 'Completed Date',
        'due_date' => 'Due Date'
    ];

    protected $revisionNullString = 'nothing';
    protected $revisionUnknownString = 'unknown';

    protected $dates = ['completed_date', 'due_date'];

    public function assignedTo()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function ticketable()
    {
        return $this->morphTo();
    }

    public function attachments()
    {
        return $this->morphMany(\App\Attachment::class, 'ticketable');
    }
}
