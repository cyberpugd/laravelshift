<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;

class ChangeTicketView extends BaseModel
{
     use Eloquence;
    public static function boot()
    {
        parent::boot();
    }
    protected $table = 'change_ticket_view';
    protected $dates = ['StartDate', 'EndDate', 'CreatedDate', 'DateCompleted'];

        protected $searchableColumns = [
               "AuditUnit","BusApprover","CancelledReason","ChangeDescription","ChangeOwner","ChangeType","CompletedType","CreatedBy","ITApprover","IsAudited","Status"
            ];

    public function getStatusAttribute($value)
    {
          return ucfirst($value);
    }

    public function getIsAuditedAttribute($value)
    {
          return ucfirst($value);
    }

    public function getChangeTypeAttribute($value)
    {
          return ucfirst($value);
    }

     public function getCompletedTypeAttribute($value)
    {
          if($value == 'imp_successfully') {
               return 'Implemented Successfully';
          } elseif($value == 'imp_with_errors') {
               return 'Implemented with errors';
          } 
          return '';
    }
}
