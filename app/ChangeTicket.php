<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Database\Eloquent\Model;
use \Venturecraft\Revisionable\RevisionableTrait;
use Auth;

class ChangeTicket extends BaseModel
{
    use RevisionableTrait, Eloquence;

    public static function boot()
    {
        parent::boot();
    }

    protected $searchableColumns = [
          'change_type',
          'status',
          'change_description',
          'roll_out_plan',
          'change_reason',
          'back_out_plan',
          'servers',
          'test_plan',
          'business_impact',
          'affected_groups',
          'cancelled_reason',
          'completed_type',
          'completed_notes',
          'changeOwner.first_name',
          'changeOwner.last_name',
          'auditUnit.name',
    ];

    protected $keepRevisionOf = [
          'audit_unit',
          'it_approver_id' ,
          'bus_approver_id' ,
          'change_owner_id',
          'change_type' ,
          'risk_class',
          'start_date',
          'end_date',
          'status',
          'is_audited'
     ];
    protected $revisionFormattedFieldNames = [
         'audit_unit' => 'Audit Unit',
         'it_approver_id' => 'IT Approver',
         'bus_approver_id' => 'Business Approver',
         'change_owner_id' => 'Change Owner',
         'change_type' => 'Change Type',
         'risk_class' => 'Risk Class',
         'start_date' => 'Start Date',
         'end_date' => 'End Date',
         'change_description' => 'Change Description',
         'roll_out_plan' => 'Roll out plan',
         'change_reason' => 'Change Reason',
         'back_out_plan' => 'Back out plan',
         'created_by' => 'Created By',
          'servers' => 'Servers',
          'test_plan' => 'Test Plan',
          'business_impact' => 'Business Impact',
          'affected_groups' => 'Affected Groups',
           'status' => 'Status',
           'is_audited' => 'Audited'
     ];

    protected $fillable = [
          'audit_unit', 'it_approver_id', 'bus_approver_id', 'change_owner_id', 'change_type', 'risk_class', 'start_date', 'end_date', 'change_description', 'roll_out_plan', 'change_reason', 'back_out_plan', 'created_by',
          'servers', 'test_plan', 'business_impact', 'affected_groups', 'status', 'completed_type', 'completed_notes', 'cancelled_reason', 'is_audited'
    ];

    protected $dates = ['start_date', 'end_date', 'close_date'];

    protected $table = 'change_tickets';

    protected $morphClass = 'ChangeTicket';

    public function workOrders()
    {
        return $this->morphMany('App\WorkOrder', 'ticketable');
    }

    public function attachments()
    {
        return $this->morphMany('App\Attachment', 'ticketable');
    }

    public function itApprover()
    {
        return $this->belongsTo('App\User', 'it_approver_id');
    }

    public function busApprover()
    {
        return $this->belongsTo('App\User', 'bus_approver_id');
    }

    public function isItApproved()
    {
        $approver = $this->changeApprovals()->where(['approved' => 1, 'approver' => $this->it_approver_id, 'approval_type' => 'it'])->count();
        if ($approver == 1) {
            return true;
        }
        return false;
    }

    public function isBusApproved()
    {
        $approver = $this->changeApprovals()->where(['approved' => 1, 'approver' => $this->bus_approver_id, 'approval_type' => 'bus'])->get();
        // $approverCount = $this->changeApprovals()->where(['approved' => 1, 'approver' => $this->bus_approver_id, 'approval_type' => 'bus'])->count();
        // dd($approver);
        if ($this->bus_approver_id == 0 && $this->isItApproved()) {
            return true;
        }
        if ($approver->isEmpty() && $this->bus_approver_id != 0) {
            return false;
        }
        if ($approver->count() == 1) {
            return true;
        }
        return false;
    }

    public function workStarted()
    {
        if ($this->status == 'in-progress' || $this->canEdit()) {
            return true;
        }
        return false;
    }

    public function changeApprovals()
    {
        return $this->hasMany('App\ChangeApproval');
    }

    public function changeOwner()
    {
        return $this->belongsTo('App\User', 'change_owner_id');
    }

    public function createdBy()
    {
        return $this->belongsTo('App\User', 'created_by');
    }

    public function canEdit()
    {
        if (!deniedPermission('change_ticket_auditor')) {
            return true;
        }
        if (($this->changeOwner->id == Auth::user()->id || $this->created_by == Auth::user()->id) && ($this->status != 'completed' || $this->status != 'cancelled')) {
            return true;
        }
        return false;
    }

    public function auditUnit()
    {
        return $this->belongsTo('App\AuditUnit', 'audit_unit');
    }

    /**
     * Accessors
     */
    public function getChangeTypeAttribute($value)
    {
        return ucfirst($value);
    }
}
