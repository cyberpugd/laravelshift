<?php

namespace App;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;
class Ticket extends BaseModel
{
     use RevisionableTrait, Eloquence;

    public static function boot()
    {
        parent::boot();
    }
    protected $revisionNullString = 'nothing';
     protected $revisionUnknownString = 'unknown';
    protected $fillable = [
    	'created_by',
    	'category_id',
    	'sub_category_id',
    	'title',
    	'description',
          'agent_id',
          'status',
          'resolution',
          'urgency_id',
    ];
    protected $searchableColumns = [
          'title',
          'status',
          'createdBy.first_name',
          'createdBy.last_name',
          'assignedToSearch.first_name',
          'assignedToSearch.last_name',
          'category.name',
          'subcategory.name',
          'description',
          'urgency.name',
          'resolution',
    ];

    protected $keepRevisionOf = array(
          'agent_id', 'sub_category_id', 'category_id', 'due_date', 'status', 'urgency_id', 'closed_date',
     );

    protected $revisionFormattedFieldNames = array(
          'agent_id' => 'Assigned To',
          'sub_category_id' => 'Subcategory',
          'category_id' => 'Category',
          'due_date' => 'Due Date',
          'status' => 'Status',
          'urgency_id' => 'Urgency',
     );

    protected $morphClass = 'Ticket';
    protected $dates = ['due_date', 'close_date'];

     protected $revisionFormattedFields = array(
         'agent_id'  => 'string:%s',
         'due_date' => 'datetime:m/d/Y g:i A',
         'created_at' => 'datetime:m/d/Y g:i A',
     );

    public function createdBy()
    {
    	return $this->belongsTo(\App\User::class, 'created_by');
    }

    public function attachments()
    {
     return $this->morphMany(\App\Attachment::class, 'ticketable');
    }

    public function workOrders()
    {
     return $this->morphMany(\App\WorkOrder::class, 'ticketable');
    }

    public function category()
    {
     return $this->belongsTo(\App\Category::class, 'category_id');
    }

    public function subcategory()
    {
     return $this->belongsTo(\App\Subcategory::class, 'sub_category_id');
    }

    public function urgency()
    {
          return $this->belongsTo(\App\Urgency::class, 'urgency_id');
    }

    public function conversations()
    {
         return $this->hasMany(\App\Conversation::class, 'ticket_id')->orderBy('created_at', 'desc');
    }

     public function conversationsPrivate()
    {
         return $this->hasMany(\App\ConversationPrivate::class, 'ticket_id')->orderBy('created_at', 'desc');
    }

    public function assignedTo()
    {
         return $this->belongsTo(\App\User::class, 'agent_id');
    }

    public function agent()
    {
         return $this->belongsTo(\App\User::class, 'agent_id');
    }

    public function assignedToSearch()
    {     
         return $this->belongsTo(\App\AssignedToSearch::class, 'agent_id');
    }


}
