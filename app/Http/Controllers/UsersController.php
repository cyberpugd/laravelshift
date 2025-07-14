<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Adldap;
use URL;
use App\Role;
use App\TicketView;
use App\ChangeTicketTemplate;
use App\ChangeTicketView;
use App\UserQueries;
use App\TicketWorkOrderView;
use App\WorkOrderTemplate;
use App\WOTemplateDetail;
use App\Http\Requests;
use App\Http\Requests\CreateViewRequest;
use App\p2helpdesk\transformers\TicketTransformer;
use App\p2helpdesk\transformers\WorkOrderTransformer;
use App\Http\Requests\UpdateAgentProfileRequest;
use App\p2helpdesk\Utilities\Timezones;
use Auth;
use App\AuditUnit;
use App\Category;
use App\Subcategory;
use Schema;
use App\Team;
use App\Urgency;
use DB;
use App\Location;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\AddUserRequest;

class UsersController extends HelpdeskController
{
    protected $ticketTransformer;
    protected $workOrderTransformer;
    protected $timezone;

    public function __construct(TicketTransformer $ticketTransformer, WorkOrderTransformer $workOrderTransformer)
    {
        $this->ticketTransformer = $ticketTransformer;
        $this->workOrderTransformer = $workOrderTransformer;
        $this->timezone = new Timezones;
    }


    public function index(Request $request)
    {
        $roles = Role::all();
        if (isset($_GET['search'])) {
            if (isset($_GET['role'])) {
                $activeUsers = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('id', $_GET['role']);
                })->where('active', 1)->search($_GET['search'])->orderBy('first_name')->paginate(25);
                $inactiveUsers = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('id', $_GET['role']);
                })->where('active', 0)->search($_GET['search'])->orderBy('first_name')->paginate(25);
            } else {
                $activeUsers = User::with('roles')->where('active', 1)->search($_GET['search'])->orderBy('first_name')->paginate(25);
                $inactiveUsers = User::with('roles')->where('active', 0)->search($_GET['search'])->orderBy('first_name')->paginate(25);
            }
            $data = [
                    'activeUsers' => $activeUsers,
                    'inactiveUsers' => $inactiveUsers,
                    'roles' => $roles,
                    'search' => $_GET['search'],
               ];
        } else {
            if (isset($_GET['role'])) {
                $activeUsers = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('id', $_GET['role']);
                })->where('active', 1)->orderBy('first_name')->paginate(25);
                $inactiveUsers = User::with('roles')->whereHas('roles', function ($query) {
                    $query->where('id', $_GET['role']);
                })->where('active', 0)->orderBy('first_name')->paginate(25);
            } else {
                $activeUsers = User::with('roles')->where('active', 1)->orderBy('first_name')->paginate(25);
                $inactiveUsers = User::with('roles')->where('active', 0)->orderBy('first_name')->paginate(25);
            }
            $data = [
               'activeUsers' => $activeUsers,
               'inactiveUsers' => $inactiveUsers,
               'roles' => $roles
          ];
        }


        return view('app.admin.user-management', $data);
    }

    public function show(Request $request)
    {
        $data = [
               'user' => User::where('id', $request->id)->firstOrFail(),
               'all_roles' => Role::all(),
               'locations' => Location::orderBy('city')->get(),
               'teams' => Team::orderBy('name')->get(),
               ];
        return view('app.admin.show-user', $data);
    }

    public function update(UpdateUserRequest $request)
    {
        $user = User::find($request->id);
        $location = Location::where('id', $request->location)->firstOrFail();
        (isset($request->roles) ? $user->assignRole($request->roles) : assignRole([]));
        $user->active = $request->active;
        (isset($request->teams) ? $user->syncTeams($request->teams) : $user->syncTeams([]));
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;
        $user->location_id = $request->location;
        $user->timezone = $location->timezone;
        $user->save();

        flash()->success(null, 'User updated');
        return redirect()->back();
    }

    public function add(Request $request, Timezones $timezone)
    {
        if (strpos($request->network_id, 'eaed1\\') === false) {
            $network_id = 'eaed1\\' . $request->network_id;
            $request->merge(['network_id' => $network_id]);
        }
        // dd($request->network_id);

        $this->validate($request, [
               'network_id' => 'required',
          ]);

        //Check if user is in helpdesk
        $hduser = User::where('ad_id', $request->network_id)->first();
        if ($hduser) {
            flash()->basicWarningStay('User already exists.');
            return redirect()->back();
        }

        //Query Active directory for the network id passed in.
        $user = Adldap::getProvider('default')->search()->users()->where('samaccountname', '=', str_replace('eaed1\\', '', $request->network_id))->get();
        if (!$user->isEmpty()) {
            //Get the location id of the user based on the city. If location doesn't exist it will default to Denver.
            $location =  $timezone->mapToLocation($user[0]['l'][0]);
            // dd($location);
            // Create the user in the database
            $user = User::create([
                    'ad_id' => strtolower($request->network_id),
                    'first_name' => $user[0]['givenname'][0],
                    'last_name' => $user[0]['sn'][0],
                    'email' => strtolower($user[0]['mail'][0]),
                    'sip' => strtolower($user[0]['userprincipalname'][0]),
                    'phone_number' => $user[0]['telephonenumber'][0],
                    'location_id' => $location->id,
                    'active' => 1
               ]);
            $user->timezone = $user->location->timezone;
            $user->save();
            // Assign new users the admin role. Need to change this to the user role before going live
            $user->assignRole([3]);
        } else {
            flash()->basicWarningStay('User does not exist in Active Directory.');
            return redirect()->back();
        }

        if (strpos(URL::previous(), 'tickets/create') !== false) {
            flash()->success(null, 'User added');
            return redirect()->back()->with('new_user', $user->id);
        } else {
            flash()->success(null, 'User added');
            return redirect('/admin/users/' . $user->id);
        }
    }

    public function createWorkOrderTemplate()
    {
        $data = [
			'users' => User::where('active', 1)->where('id', '!=', auth()->user()->id)->get(),
            'templates' => WorkOrderTemplate::with(['templateDetail'])->where('owner_id', Auth::user()->id)->orderBy('name')->get(),
        ];
        return view('app.user-settings.create-wo-template', $data);
    }

	public function updateWorkOrderTemplate($templateId)
	{
		$data = [
			'users' => User::where('active', 1)->get(),
			'template' => WorkOrderTemplate::with(['templateDetail', 'users'])->where('owner_id', Auth::user()->id)->where('id', $templateId)->firstOrFail(),
		];
		return view('app.user-settings.update-wo-template', $data);
	}

    public function saveWorkOrderTemplate(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:255',
               'shared_with' => 'array',
               'shared_with.*' => 'exists:users,id'
        ]);
        
        $work_order_template = WorkOrderTemplate::create([
               'name' => $request->name,
               'owner_id' => Auth::user()->id
        ]);

        // Share work order template with selected users
        if(!collect($request->shared_with)->isEmpty()) {
            $work_order_template->users()->sync($request->shared_with);
        }
        flash()->success(null, 'Template Created');
        return redirect()->back();
    }

    public function saveWorkOrderDetail(Request $request)
    {
        $this->validate($request, [
               'template_id' => 'required|numeric|exists:work_order_templates,id',
               'subject' => 'required|max:255',
               'assigned_to' => 'required|numeric|exists:users,id',
               'due_in' => 'required|numeric|between:-1,20'
          ]);
        $wo_template = WorkOrderTemplate::where('id', $request->template_id)->firstOrFail();
        $wo_template->templateDetail()->create([
               'template_id' => $request->template_id,
               'assigned_to' => $request->assigned_to,
               'subject' => $request->subject,
               'work_requested' => $request->work_requested,
               'due_in' => $request->due_in
          ]);
        flash()->success(null, 'Work order added');
        return redirect()->back();
    }

    public function showProfile()
    {
        $data = [
               'user' => Auth::user(),
               'locations' => Location::orderBy('city')->get(),
               'teams' => Team::where('self_enroll', 1)->orderBy('name')->get(),
               'timezones' => $this->timezone->all(),
         ];

        // dd(Auth::user()->teams->whereIn('id', Team::where('self_enroll', 0)->get()->lists('id')->toArray())->lists('id')->toArray());
        // dd(Team::where('self_enroll', 0)->get()->lists('id')->toArray());

        return view('app.user-settings.profile', $data);
    }

    public function updateProfile(UpdateAgentProfileRequest $request)
    {
        $user = Auth::user();
        // $location = Location::where('id', $request->location)->firstOrFail();
        $teamsArray = Auth::user()->teams->whereIn('id', Team::where('self_enroll', 0)->get()->lists('id')->toArray())->lists('id')->toArray();
        $user->update([
               'first_name' => $request->first_name,
               'last_name' => $request->last_name,
               'email' => $request->email,
               'location_id' => $request->location,
               'timezone' => $request->timezone,
               'out_of_office' => $request->status
          ]);
        if (isset($request->teams)) {
            foreach ($request->teams as $team) {
                array_push($teamsArray, $team);
            }
            $user->syncTeams($teamsArray);
        } else {
            $user->syncTeams([]);
        }
        flash()->success(null, 'Profile Updated.');
        return redirect()->back();
    }

    public function showQueryBuilder()
    {
        $ticket_view = TicketView::first();
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
        // dd($ticket_view);
        $ticket_columns = [];
        foreach ($ticket_view['attributes'] as $key => $value) {
            if ($key != 'ID') {
                $ticket_columns[$key] = $key;
            }
        }
        ksort($ticket_columns);

        $change_ticket_columns = [];
        foreach ($change_ticket_view['attributes'] as $key => $value) {
            if ($key != 'ID') {
                $change_ticket_columns[$key] = $key;
            }
        }
        ksort($change_ticket_columns);

        $ticket_work_order_columns = [];
        foreach ($ticket_work_order_view['attributes'] as $key => $value) {
            if ($key != 'ID' && $key != 'Type') {
                $ticket_work_order_columns[$key] = $key;
            }
        }
        ksort($ticket_work_order_columns);
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
          ];
        // array_keys($data['ticket_columns']);
        // dd(array_keys($data['ticket_columns']));
        return view('app.user-settings.query-builder-new', $data);
    }

    public function createUserView(CreateViewRequest $request)
    {
        $data = $request->all();


        $where = $data['whereClause'];

        //Need to save this sql statement somewhere.
        $selectedColumns = rtrim(implode(', ', $data['selectedColumns']), ', ');

        $query = Auth::user()->queries()->create([
               'name' => $data['name'],
               'query_type' => $data['queryType'],
               'columns' => $selectedColumns,
               'where_clause' => $where,
               'sort_by' => $data['sortBy'],
               'sort_direction' => $data['sortDirection'],
          ]);

        flash()->success(null, 'View Created Successfully.');
        return $query;
    }

    public function createTicketView(CreateViewRequest $request)
    {
        //Vaidate the request
        // dd($request);

        //database column mapping
        $ticket_view = '';
        if ($request->query_type == 'ticket') {
            $ticket_view = TicketView::first();
        } elseif ($request->query_type == 'ticket_work_order') {
            $ticket_view = TicketWorkOrderView::first();
        } elseif ($request->query_type == 'change_ticket') {
            $ticket_view = ChangeTicketView::first();
        }
        $columns = [];
        foreach ($ticket_view['attributes'] as $key => $value) {
            if ($key != 'ID' && $key != 'Type') {
                $columns[$key] = $key;
            }
        }

        $select = null;
        if ($request->query_type == 'ticket_work_order') {
            $select = $select . 'ID' . ',' . 'Type' . ',';
        } else {
            $select = $select . 'ID' . ',';
        }
        // Loop through array values and create string
        foreach ($request->select_columns as $select_column) {
            foreach ($columns as $key => $value) {
                if ($key == $select_column) {
                    $select = $select . '[' . $key . ']' . ',';
                }
            }
        }
        $select = rtrim($select, ",");
        //Store $select into database column removing the trailing comma
        // End Select column builder
        // dd($request);
        // Begin Where clause builder
        $filter = [];
        if (isset($request->filter_column)) {
            foreach ($request->filter_column as $filter_column) {
                if (isset($filter_column[3])) {
                    array_push($filter, [$filter_column[0], $filter_column[1], $filter_column[2], $filter_column[3]]);
                } else {
                    // if($filter_column[1] == 'like'){
                    //      array_push($filter, [$filter_column[0], $filter_column[1], "'%" . $filter_column[2] . "%'"]);
                    // } else {
                    array_push($filter, [$filter_column[0], $filter_column[1], $filter_column[2]]);
                }
            }
        }


        $whereString = null;
        $view = Auth::user()->views()->create([
                    'query_type' => $request->query_type,
                    'name' => $request->name,
                    'select_columns' => $select,
               ]);
        foreach ($filter as $i) {
            $view->filters()->create([
                         'view_id' => $view->id,
                         'column' => $i[0],
                         'operator' => $i[1],
                         'criteria1' => $i[2],
                         'criteria2' => (isset($i[3]) ? $i[3] : null)
                    ]);
        }
        flash()->success(null, 'View Created.');
        return redirect()->back();
    }

    public function deleteWorkOrderTemplate(Request $request)
    {
        $work_order = WorkOrderTemplate::with(['users', 'templateDetail'])->where('id', $request->id)->firstOrFail();
		$work_order->users()->detach();
		$work_order->templateDetail()->delete();
        $work_order->delete();
        flash()->success(null, 'Template Removed.');
        return redirect('/user-settings/wo-template');
    }

    public function getWorkOrderForTemplate(Request $request)
    {
        $work_order = WOTemplateDetail::where('id', $request->id)->firstOrFail();
        return $work_order;
    }

    public function updateWorkOrderDetail(Request $request)
    {
        $wo_detail = WOTemplateDetail::where('id', $request->id)->firstOrFail();
        $wo_detail->update([
               'assigned_to' => $request->assigned_to,
               'subject' => $request->subject,
               'work_requested' => $request->work_requested,
               'due_in' => $request->due_in
          ]);
        flash()->success(null, 'Work order updated');
        return redirect()->back();
    }

    public function deleteWOFromTemplate(Request $request)
    {
        $wo_detail = WOTemplateDetail::where('id', $request->id)->firstOrFail();
        $wo_detail->delete();
        flash()->success(null, 'Work Order removed successfully');
        return redirect()->back();
    }

    public function editTemplateName(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:255',
			   'shared_with' => 'array',
               'shared_with.*' => 'exists:users,id'
          ]);
        $template = WorkOrderTemplate::where('id', $request->id)->firstOrFail();
        $template->name = $request->name;
        $template->save();

		if(!isset($request->shared_with)) {
			$template->users()->detach();
		} else {
			if(!collect($request->shared_with)->isEmpty()) {
				$template->users()->sync($request->shared_with);
			}
		}
		
        flash()->success(null, 'Template updated.');
        return redirect()->back();
    }

    public function showCCTemplates()
    {
        $data = [
               'templates' => ChangeTicketTemplate::where('owner_id', Auth::user()->id)->orderBy('name')->get(),
         ];

        return view('app.user-settings.show-cc-templates', $data);
    }

    public function createCCTemplate()
    {
        $user = Auth::user();
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();
        $data = [
          'user' => $user,
          'users' => User::where('active', 1)->orderBy('first_name')->get(),
          'categories' => $categories,
          'urgencyrows' => Urgency::all(),
          'audit_units' => AuditUnit::where('status', 1)->get(),
          ];
        return view('app.user-settings.create-cc-template', $data);
    }

    public function showCCTemplate(Request $request)
    {
        $user = Auth::user();
        $categories = Category::with(['subcategories' => function ($query) {
            $query->where('active', 1)->orderBy('name');
        }])->where('active', 1)->orderBy('name')->get();
        $data = [
          'user' => $user,
          'users' => User::where('active', 1)->orderBy('first_name')->get(),
          'categories' => $categories,
          'urgencyrows' => Urgency::all(),
          'audit_units' => AuditUnit::where('status', 1)->get(),
          'template' => ChangeTicketTemplate::with('sharedWith')->where('id', $request->id)->first(),
          ];
        // dd($data);
        return view('app.user-settings.edit-cc-template', $data);
    }

    public function saveCCTemplate(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:100',
               'start_date' => 'date|date_format:m/d/Y g:i a',
               'end_date' => 'date|date_format:m/d/Y g:i a|after:start_date',
          ]);
        // dd($request);
        $request->request->add(['owner_id' => Auth::user()->id]);
        $shared_with = $request->share_with;
        $request->request->remove('share_with');
        $template = ChangeTicketTemplate::create($request->all());

        if (isset($shared_with)) {
            $template->sharedWith()->sync($shared_with);
        } else {
            $template->sharedWith()->sync([]);
        }
        flash()->success(null, 'Template Created');
        return redirect('/user-settings/cc-templates');
    }

    public function updateCCTemplate(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:100',
               'start_date' => 'date|date_format:m/d/Y g:i a',
               'end_date' => 'date|date_format:m/d/Y g:i a|after:start_date',
          ]);
        // dd($request);
        $shared_with = $request->share_with;
        $request->request->remove('share_with');
        $template = ChangeTicketTemplate::where('id', $request->id)->firstOrFail();
        $template->update($request->all());

        if (isset($shared_with)) {
            $template->sharedWith()->sync($shared_with);
        } else {
            $template->sharedWith()->sync([]);
        }
        flash()->success(null, 'Template Updated');
        return redirect()->back();
    }

    public function deleteCCTemplate(Request $request)
    {
        $template = ChangeTicketTemplate::where('id', $request->id)->where('owner_id', Auth::user()->id)->firstOrFail();
        $template->delete();
        flash()->success(null, 'Template Deleted');
        return redirect()->back();
    }

    public function saveMenuState(Request $request)
    {
        $user = User::where('id', Auth::user()->id)->first();
        $user->is_menu_collapsed = !$user->is_menu_collapsed;
        $user->save();
        return response()->json([
                         'status' => 'success',
                         'message' => 'Menu state saved'
                    ]);
    }
}
