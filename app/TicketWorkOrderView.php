<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Sofa\Eloquence\Eloquence;

class TicketWorkOrderView extends BaseModel
{
     use Eloquence;
    protected $table = 'ticket_work_order';
    protected $dates = ['DueDate', 'DateCompleted'];

    protected $searchableColumns = [
          'AssignedTo',
          'CreatedBy',
          'Status',
          'Subject',
          'WorkCompleted',
          'WorkRequested',
    ];
}
