<?php

namespace App\Http\Controllers\Portal;

use App\Ticket;
use App\AdminSettings;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SurveyController extends Controller
{
    public function show()
    {
        if (!isset(request()->ticket_no)) {
            return redirect('/');
        }
        $ticket = Ticket::where('id', request()->ticket_no)->firstOrFail();
        $survey_link = $ticket->assignedTo->surveyGroup->survey_link;
        $data = [
            'survey_link' => $survey_link . '?ticket_no=' . request()->ticket_no
        ];

        return view('app.survey.show', $data);
    }
}
