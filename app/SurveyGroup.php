<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SurveyGroup extends Model
{
    protected $guarded = [];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function syncAgents($agents)
    {
        // dd($agents);
        User::whereIn('id', $agents)->update([
            'survey_group_id' => 1,
        ]);
        User::whereNotIn('id', $agents)->update([
            'survey_group_id' => 0,
        ]);
        return $this;
    }
}
