<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\CreateViewRequest;
use Auth;
use DB;
use PDF;
use Excel;
use App\Category;
use App\Subcategory;
use App\User;
use App\Location;
use App\Urgency;
use Carbon\Carbon;
use App\AuditUnit;
use App\TicketView;
use App\TicketWorkOrderView;
use App\ChangeTicketView;
use App\UserView;
use App\UserQueries;

class ViewController extends Controller
{
    public function show(Request $request)
    {
        $search = ($request->get('search') != null ? $request->get('search') : null);
        $view = UserQueries::where(['id' => $request->id, 'user_id' => Auth::user()->id])->firstOrFail();
        $direction = ($request->get('direction') != null ? $request->get('direction') : ($view->sort_direction != null ? $view->sort_direction : 'asc'));
        $sortBy = ($request->get('sortBy') != null ? $request->get('sortBy') : ($view->sort_by != null ? $view->sort_by : 'ID'));
        $model = '';
        if ($view->query_type == 'ticket') {
            $model = 'App\TicketView';
        } elseif ($view->query_type == 'work_order') {
            $model = 'App\TicketWorkOrderView';
        } elseif ($view->query_type == 'change_control') {
            $model = 'App\ChangeTicketView';
        }
        $where = $view->where_clause;
        if (strpos($where, 'today+')) {
            //replace today+$i with dateAdd on getdate()
            $daysToAdd = substr($where, strpos($where, "+") +1);
            $daysToAdd = substr($daysToAdd, 0, strpos($daysToAdd, "'"));
            // return 'today+'.$daysToAdd;
            $replaceString = "'today+$daysToAdd'";
            $where = str_replace($replaceString, "'" . Carbon::now()->timezone(Auth::user()->timezone)->addDays($daysToAdd)->startOfDay() . "'", $where);
        }
        if (strpos($where, 'today-')) {
            //replace today-$i with dateAdd on getdate()
            $daysToAdd = substr($where, strpos($where, "-") +1);
            $daysToAdd = substr($daysToAdd, 0, strpos($daysToAdd, "'"))*-1;
            $replaceString = "'today$daysToAdd'";
            $where = str_replace($replaceString, "'" . Carbon::now()->timezone(Auth::user()->timezone)->addDays($daysToAdd)->startOfDay() . "'", $where);
        }
        if (strpos($where, 'today')) {
            $where = str_replace("'today'", "'" . Carbon::now()->timezone(Auth::user()->timezone)->startOfDay() . "'", $where);
        }
        // dd($where);
        $firstColumns = ($view->query_type == 'work_order' ? 'ID, Type, ' : 'ID, ');
        try {
            if (isset($search)) {
                if (is_numeric($search)) {
                    $results = $model::select(DB::raw($firstColumns . $view->columns))
                              ->where('id', $search);
                } else {
                    $results = $model::select(DB::raw($firstColumns . $view->columns))
                              ->whereRaw($where)->orderBy($sortBy, $direction)->search($search, null, true, 1);
                }
            } else {
                $results = $model::select(DB::raw($firstColumns . $view->columns))
                    ->whereRaw($where)->orderBy($sortBy, $direction);
            }



            // dd($results);
            $data = [
                         'results' => ($request->get('print') == true ? $results->get() : $results->paginate(15)),
                         'view' => $view,
                         'search' => $search
                    ];
            if ($request->get('print') == true) {
                Excel::create($data['view']->name, function ($excel) use ($data) {
                    $excel->sheet($data['view']->name, function ($sheet) use ($data) {
                        $sheet->fromArray($data['results']->toArray());
                    });
                })->export('xlsx');
            }
        } catch (\Illuminate\Database\QueryException $e) {
            flash()->basicWarningStay('Query Error: There was an issue with your database query, please check your filter criteria and be sure you are using correct formatting.');
            return redirect('/views/edit/' . $view->id);
        }
        // dd($data['results']);
        return view('app.tickets.show-view', $data);
    }


    public function editView(Request $request)
    {
        $ticket_view = TicketView::first();
        $query = UserQueries::where('id', $request->id)->where('user_id', Auth::user()->id)->firstOrFail();
        $change_ticket_view = ChangeTicketView::first();
        $ticket_work_order_view = TicketWorkOrderView::first();
        $categories = Category::select('name')->where('active', 1)->orderBy('name')->get();
        $subcategories = Subcategory::select('name')->where('active', 1)->orderBy('name')->get();
        $users = User::select(DB::raw("first_name + ' ' + last_name as name"))->where(['active' => 1])->orderBy('first_name')->get();
        $approvers = User::select(DB::raw("first_name + ' ' + last_name as name"))->where(['active' => 1])->whereHas('roles', function ($query) {
            $query->whereHas('permissions', function ($query2) {
                $query2->where('name', 'approve_change_ticket');
            });
        })->orderBy('first_name')->get();

        $agents = User::select(DB::raw("first_name + ' ' + last_name as name"))->where(['active' => 1])->whereHas('roles', function ($query) {
            $query->whereHas('permissions', function ($query2) {
                $query2->where('name', 'be_assigned_ticket');
            });
        })->orderBy('first_name')->get();

        $locations = Location::select('city')->orderBy('city');
        $urgencies = Urgency::select('name')->orderBy('name');
        $audit_units = AuditUnit::select('name')->orderBy('name');
        // dd(explode(',', str_replace(' ', '', $query->columns)));
        $ticket_columns = [];
        foreach ($ticket_view['attributes'] as $key => $value) {
            if ($key != 'ID' && !in_array($key, explode(',', str_replace(' ', '', $query->columns)))) {
                $ticket_columns[$key] = $key;
            }
        }
        ksort($ticket_columns);

        $change_ticket_columns = [];
        foreach ($change_ticket_view['attributes'] as $key => $value) {
            if ($key != 'ID' && !in_array($key, explode(',', str_replace(' ', '', $query->columns)))) {
                $change_ticket_columns[$key] = $key;
            }
        }
        ksort($change_ticket_columns);

        $ticket_work_order_columns = [];
        foreach ($ticket_work_order_view['attributes'] as $key => $value) {
            if ($key != 'ID' && $key != 'Type' && !in_array($key, explode(',', str_replace(' ', '', $query->columns)))) {
                $ticket_work_order_columns[$key] = $key;
            }
        }
        ksort($ticket_work_order_columns);
        // dd(json_encode(explode(',', str_replace(', ', ',', $query->columns))));
        $data = [
               'ticket_columns' => json_encode(array_keys($ticket_columns)),
               'change_ticket_columns' => json_encode(array_keys($change_ticket_columns)),
               'ticket_work_order_columns' => json_encode(array_keys($ticket_work_order_columns)),
               'categories' => $categories,
               'subcategories' => $subcategories,
               'users' => $users,
               'agents' => $agents,
               'approvers' => $approvers,
               'locations' => $locations,
               'urgencies' => $urgencies,
               'audit_units' => $audit_units,
               'query' => $query,
          ];
        // array_keys($data['ticket_columns']);
        // dd(array_keys($data['ticket_columns']));
        return view('app.user-settings.edit-user-view', $data);
    }

    public function updateView(CreateViewRequest $request)
    {
        $data = $request->all();
        $where = $data['whereClause'];
        //Need to save this sql statement somewhere.
        $selectedColumns = rtrim(implode(', ', $data['selectedColumns']), ', ');

        $query = Auth::user()->queries()->where('id', $data['query_id'])->update([
                    'name' => $data['name'],
                    'query_type' => $data['queryType'],
                    'columns' => $selectedColumns,
                    'where_clause' => $where,
                    'sort_by' => $data['sortBy'],
                    'sort_direction' => $data['sortDirection'],
               ]);

        if ($query) {
            return ['status' => 'success'];
        }
        return ['status' => 'error'];
    }

    public function deleteView(Request $request)
    {
        $view = UserQueries::where(['id' => $request->id, 'user_id' => Auth::user()->id])->firstOrFail();
        $view->delete();
        flash()->success(null, 'View Removed.');
        return redirect('/dashboard');
    }
}
