<?php

namespace App\Http\Controllers;

use App\User;
use App\SurveyGroup;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SurveyGroupController extends Controller
{
    public function index()
    {
        $data = [
            'survey_groups' => SurveyGroup::all(),
        ];
        return view('app.admin.survey-groups.index', $data);
    }

    public function show($id)
    {
        $data = [
            'survey_group' => SurveyGroup::with('users')->where('id', $id)->firstOrFail(),
            'agents' => User::whereHas('roles.permissions', function ($query) {
                $query->where('name', 'be_assigned_ticket');
            })->where('active', 1)
            ->where(function ($query) use ($id) {
                $query->where('survey_group_id', $id)
                        ->orWhere('survey_group_id', 0);
            })
            ->orderBy('first_name')->get(),
        ];
        // dd($data);
        return view('app.admin.survey-groups.show', $data);
    }

    public function update(Request $request)
    {
        $survey_group = SurveyGroup::where('id', $request->id)->firstOrFail();
        $survey_group->update([
            'name' => $request->name,
            'survey_link' => $request->survey_link,
        ]);
        $survey_group->syncAgents($request->agents);
        flash()->success(null, 'Group Saved');
        return redirect('/admin/survey-groups/' . $survey_group->id);
    }

    public function save(Request $request)
    {
        $survey_group = SurveyGroup::create([
            'name' => $request->name,
            'survey_link' => $request->survey_link,
        ]);

        flash()->success(null, 'Group Created');
        return redirect('/admin/survey-groups/' . $survey_group->id);
    }

    public function destroy($id)
    {
        $survey_group = SurveyGroup::where('id', $id)->firstOrFail();
        $survey_group->delete();
        flash()->success(null, 'Group Deleted');
        return redirect('/admin/survey-groups');
    }
}
