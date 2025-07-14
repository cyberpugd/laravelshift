<?php

namespace App;
use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class TicketView extends BaseModel
{
     use Eloquence;
     
    protected $table = 'ticket_view';
    protected $dates = ['DueDate', 'DateCreated', 'DateClosed'];

    protected $searchableColumns = [
                    'Description'
                    ,'Subject'
                    , 'Status'
                    ,'Resolution'
                    ,'Category'
                    ,'Subcategory'
                    ,'CreatedBy'
                    ,'AssignedTo'
                    ,'Urgency'
                    ,'AssignedToLocation'
                         ,'CreatedByLocation'
    ];

     public function getStatusAttribute($value)
    {
          return ucfirst($value);
    }
}
