<?php

namespace App;

use Sofa\Eloquence\Eloquence;
use Illuminate\Foundation\Auth\User as Authenticatable;
use \Venturecraft\Revisionable\RevisionableTrait;

class User extends Authenticatable
{
    use RevisionableTrait, Eloquence;

    protected $searchableColumns = [
          'first_name',
          'last_name',
          'ad_id',
          'email',
    ];

    public static function boot()
    {
        parent::boot();
    }
    public function identifiableName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'ad_id', 'email', 'password','phone_number','timezone','sip','location_id','out_of_office'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    public function tickets()
    {
        return $this->hasMany('App\Ticket', 'created_by', 'id');
    }

    public function workOrdersAssigned()
    {
        return $this->hasMany(\App\WorkOrder::class, 'assigned_to', 'id');
    }

    public function workOrdersCreated()
    {
        return $this->hasMany(\App\WorkOrder::class, 'created_by', 'id');
    }

    public function workOrderTemplates()
    {
        return $this->hasMany(\App\WorkOrderTemplate::class, 'owner_id', 'id');
    }

    public function sharedWorkOrderTemplates()
    {
        return $this->belongsToMany(\App\WorkOrderTemplate::class);
    }

    public function subcategoriesCreated()
    {
        return $this->hasMany(\App\Subcategory::class, 'created_by', 'id');
    }

    public function roles()
    {
        return $this->belongsToMany(\App\Role::class);
    }

    public function assignRole($roles)
    {
        return $this->roles()->sync($roles);
    }

    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('label', $role);
        }
        return !! $role->intersect($this->roles)->count();
    }

    public function location()
    {
        return $this->belongsTo(\App\Location::class);
    }

    public function forms()
    {
        return $this->belongsToMany(\App\UserForm::class, 'user_user_form', 'user_id', 'form_id');
    }

    public function views()
    {
        return $this->hasMany(\App\UserView::class)->orderBy('name');
    }

    public function queries()
    {
        return $this->hasMany(\App\UserQueries::class)->orderBy('name');
    }

    public function teams()
    {
        return $this->belongsToMany(\App\Team::class);
    }

    public function hasTeam($team)
    {
        if (is_string($team)) {
            return $this->teams->contains('name', $team);
        }
        return !! $team->intersect($this->teams)->count();
    }

    public function syncTeams($teams)
    {
        return $this->teams()->sync($teams);
    }


    public function ccTemplates()
    {
        return $this->belongsToMany(\App\ChangeTicketTemplate::class);
    }

    public function surveyGroup()
    {
        return $this->belongsTo(\App\SurveyGroup::class);
    }
}
