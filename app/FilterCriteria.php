<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FilterCriteria extends BaseModel
{
    protected $table = 'filter_criteria';
    protected $fillable = ['view_id', 'column', 'operator', 'criteria1', 'criteria2'];

    public function view()
    {
         return $this->belongsTo('App\UserView');
    }
}
