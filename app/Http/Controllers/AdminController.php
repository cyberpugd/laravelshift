<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\UpdateUserFormRequest;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\CreateAnnouncementRequest;
use App\Exceptions\InternalServerException;
use App\AdminSettings;
use App\Role;
use App\p2helpdesk\classes\Email\EmailProvider;
use App\UserForm;
use App\FormColumn;
use App\Urgency;
use Auth;
use App\User;
use App\Permission;
use App\Team;
use App\ticket as Ticket;
use App\Subcategory;
use App\Category;
use App\AuditUnit;
use App\Location;
use App\Announcement;
use App\Holiday;
use Exception;
use Carbon\Carbon;
use DB;
use App\p2helpdesk\Utilities\Timezones;

class AdminController extends HelpdeskController
{
    public function showMailSetup(Request $request)
    {
        $settings = AdminSettings::first();
        $data = [
               'settings' => $settings
         ];
        return view('app.admin.mail-setup', $data);
    }

    public function postMailSetup(Request $request)
    {
        $this->validate($request, [
               'mail_port' => 'required|numeric',
               'mail_server' => 'required|max:255',
               'mail_user' => 'required|max:255|email',
               'mail_password' => 'required|max:255',
               'email_address' => 'required|max:255|email',
               'mail_folder' => 'required|max:255',
               'mail_processed_folder' => 'required|max:255',
               'phone_number' => 'max:100',
          ]);
        $settings = AdminSettings::first();
        if (!$settings) {
            AdminSettings::create([
                  'mail_port' => $request->mail_port,
                  'mail_server' => $request->mail_server,
                  'mail_user' => $request->mail_user,
                  'mail_password' => $request->mail_password,
                  'mail_folder' => $request->mail_folder,
                  'mail_processed_folder' => $request->mail_processed_folder,
                  'email_address' => $request->email_address,
                  'phone_number' => $request->phone_number,
             ]);
            flash()->success(null, 'Settings Updated');
            return redirect()->back();
        }

        $settings->mail_port = $request->mail_port;
        $settings->mail_server = $request->mail_server;
        $settings->mail_user = $request->mail_user;
        $settings->mail_password = $request->mail_password;
        $settings->email_address = $request->email_address;
        $settings->mail_folder = $request->mail_folder;
        $settings->mail_processed_folder = $request->mail_processed_folder;
        $settings->phone_number = $request->phone_number;
        $settings->save();
        flash()->success(null, 'Settings Updated');
        return redirect()->back();
    }

    public function showRoles(Request $request)
    {
        $data = [
               'roles' => Role::with('permissions')->orderBy('label')->get(),
               'permissions' => Permission::orderBy('name')->get()
         ];
        return view('app.admin.show-roles', $data);
    }

    public function editRole(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:255',
          ]);
        $role = Role::findOrFail($request->id);

        $role->label = $request->name;
        $role->save();

        flash()->success(null, 'Role name updated.');
        return redirect()->back();
    }

    public function updateRolePermissions(Request $request)
    {
        $role = Role::findOrFail($request->id);
        $role->syncPermissions($request->permissions);
        flash()->success(null, 'Role permissions updated');
        return redirect()->back();
    }

    public function saveRole(CreateRoleRequest $request)
    {
        $this->validate($request, [
               'label' => 'required|max:255',
          ]);
        Role::create([
               'label' => $request->label,
          ]);
        flash()->success(null, 'Role created');
        return redirect()->back();
    }

    public function manageAuditUnit()
    {
        $data = [
               'audit_units' => AuditUnit::all()
          ];
        return view('app.admin.audit-unit-management', $data);
    }

    public function createAuditUnit(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:255',
          ]);
        AuditUnit::create(['name' => $request->name, 'status' => 1]);
        flash()->success(null, 'Audit Unit Created');
        return redirect()->back();
    }

    public function editAuditUnit(Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
               'name' => 'required|max:255',
               'status' => 'required|numeric|in:1,0',
          ]);
        try {
            DB::beginTransaction();
            $audit_unit = AuditUnit::findOrFail($request->id);
            $audit_unit->status = $request->status;
            $audit_unit->name = $request->name;
            $audit_unit->save();
        } catch (Exception $e) {
            DB::rollback();
            return response()->json([
               'result' => $e->getMessage(),
               'color' => 'red'
            ], 200);
        }
        DB::commit();
        $audit_unit = AuditUnit::findOrFail($request->id);
        return response()->json([
                   'status'  => $audit_unit->status,
                   'name' => $audit_unit->name,
                   'result' => 'Update Successful',
                   'color' => 'green'
               ], 200);
    }

    public function showLocations(Timezones $timezones)
    {
        $locations = Location::with('holidays')->orderBy('city')->get();
        $holidays = Holiday::orderBy('date')->get();
        $data = [
               'locations' => $locations,
               'holidays' => $holidays,
               'timezones' => $timezones->all()
         ];
        return view('app.admin.show-locations', $data);
    }

    public function addLocation(Request $request)
    {
        $this->validate($request, [
               'city' => 'required|max:255',
               'timezone' => 'required|max:255',
          ]);
        Location::create([
               'city' => $request->city,
               'timezone' => $request->timezone
          ]);
        flash()->success(null, 'Location added.');
        return redirect()->back();
    }
    public function updateLocation(UpdateLocationRequest $request)
    {
        $location = Location::findOrFail($request->id);
        if (isset($request->holidays)) {
            $location->syncHolidays(collect($request->holidays)->pluck('id')->toArray());
        } else {
            $location->syncHolidays([]);
        }
        return response()->json([
                    'location_id' => $location->id,
                   'result' => 'Update Successful',
                   'color' => 'green'
               ], 200);
    }

    public function showHolidays()
    {
        $data = [
               'holidays' => Holiday::orderBy('date')->get()
         ];
        return view('app.admin.show-holidays', $data);
    }

    public function removeHoliday(Request $request)
    {
        $holiday = Holiday::where('id', $request->id)->firstOrFail();
        $holiday->delete();
        return response()->json([
                   'result' => 'Update Successful',
                   'color' => 'green'
               ], 200);
    }

    public function addHoliday(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:50',
               'date' => 'required|date|date_format:"M d, Y"'
          ]);
        $date = Carbon::createFromFormat('M d, Y', $request->date);
        Holiday::create([
               'name' => $request->name,
               'date' => $date
          ]);
        flash()->success(null, 'Holiday Created.');
        return redirect()->back();
    }

    public function updateHoliday(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:50',
               'date' => 'required|date|date_format:"M d, Y"',
          ]);
        $date = Carbon::createFromFormat('M d, Y', $request->date);
        $holiday = Holiday::where('id', $request->id)->firstOrFail();
        $holiday->update([
               'name' => $request->name,
               'date' => $date
          ]);
        return response()->json([], 200);
    }

    public function showTeams()
    {
        $data = [
               'teams' => Team::orderBy('name')->get(),
               'subcategories' => Subcategory::where('active', 1)->orderBy('name')->get(),
               'categories' => Category::where('active', 1)->orderBy('name')->get(),
               'agents' => User::whereHas('roles.permissions', function ($query) {
                   $query->where('name', 'be_assigned_ticket');
               })->where('active', 1)->orderBy('first_name')->get(),
          ];
        return view('app.admin.show-teams', $data);
    }

    public function showTeam(Request $request)
    {
        //!in_array($subcategory->id, $team->subcategories->lists('id')->toArray())
        $team = Team::with(['users', 'users.location', 'subcategories.category'])->where('id', $request->id)->firstOrFail();

        $data = [
               'team' => $team,
               'availableSubcategories' => Subcategory::with('category')
                    ->join('categories', 'subcategories.category_id', '=', 'categories.id')
                    ->where('subcategories.active', 1)
                    ->whereNotIn('subcategories.id', $team->subcategories->lists('id')->toArray())
                    ->orderBy('categories.name')
                    ->orderBy('subcategories.name')
                    ->get(['subcategories.id', 'subcategories.name', 'categories.name as category_name']),
               // 'selectedSubcategories' => Subcategory::with('category')->where('active', 1)->whereIn('id', $team->subcategories->lists('id')->toArray())->orderBy('name')->get(),
               'categories' => Category::where('active', 1)->orderBy('name')->get(),
               'agents' => User::with('location')->whereHas('roles.permissions', function ($query) {
                   $query->where('name', 'be_assigned_ticket');
               })->whereNotIn('id', $team->users->lists('id')->toArray())->where('active', 1)->orderBy('first_name')->get(),
         ];

        return view('app.admin.show-team', $data);
    }

    public function createTeam(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:255',
          ]);
        Team::create([
               'name' => $request->name,
               'self_enroll' => (isset($request->self_enroll) ? 1 : 0)
          ]);
        flash()->success(null, 'Team Created.');
        return redirect()->back();
    }

    public function editTeam(Request $request)
    {
        $this->validate($request, [
               'name' => 'required|max:255',
          ]);
        $team = Team::where('id', $request->id)->firstOrFail();
        $team->name = $request->name;
        $team->save();
        flash()->success(null, 'Team Updated.');
        return redirect()->back();
    }

    public function syncTeamWithSubcategories(Request $request)
    {
        $team = Team::where('id', $request->id)->firstOrFail();
        if (isset($request->subcategories)) {
            $team->syncSubcategories(collect($request->subcategories)->lists('id')->toArray());
        } else {
            $team->syncSubcategories([]);
        }
        $team->self_enroll = ($request->self_enroll ? 1 :  0);
        $team->save();
        return $team;
    }

    public function announcements()
    {
        $announcements = Announcement::orderBy('start_date', 'desc')->paginate(4);
        $data = [
               'announcements' => $announcements,
         ];
        return view('app.admin.announcements', $data);
    }

    public function createAnnouncement(CreateAnnouncementRequest $request)
    {
        $start_date = Carbon::createFromFormat('m/d/Y h:i A', $request->start_date, Auth::user()->timezone)->timezone('utc');
        $end_date = Carbon::createFromFormat('m/d/Y h:i A', $request->end_date, Auth::user()->timezone)->timezone('utc');
        Announcement::create([
               'type' => $request->type,
               'location' => $request->location,
               'title' => $request->title,
               'details' => $request->details,
               'start_date' => $start_date,
               'end_date' => $end_date,
          ]);

        flash()->success(null, 'Announcement Created!');
        return redirect()->back();
    }

    public function deleteAnnouncement(Request $request)
    {
        $announcement = Announcement::findOrFail($request->id);
        $announcement->delete();
        flash()->success(null, 'Announcement Deleted!');
        return redirect()->back();
    }

    public function editAnnouncement(CreateAnnouncementRequest $request)
    {
        $announcement = Announcement::findOrFail($request->id);
        $start_date = Carbon::createFromFormat('m/d/Y h:i A', $request->start_date, Auth::user()->timezone)->timezone('utc');
        $end_date = Carbon::createFromFormat('m/d/Y h:i A', $request->end_date, Auth::user()->timezone)->timezone('utc');
        $announcement->update([
               'type' => $request->type,
               'location' => $request->location,
               'title' => $request->title,
               'details' => $request->details,
               'start_date' => $start_date,
               'end_date' => $end_date
          ]);
        flash()->success(null, 'Announcement Updated!');
        return redirect()->back();
    }

    public function expireAnnouncement(Request $request)
    {
        $announcement = Announcement::findOrFail($request->id);
        $announcement->end_date = Carbon::now()->subSecond();
        $announcement->save();
        flash()->success(null, 'Announcement Expired.');
        return redirect()->back();
    }

    public function loginAsUser(Request $request)
    {
        Auth::logout();
        $user = User::where('id', $request->id)->firstOrFail();
        Auth::login($user);
        flash()->confirm(null, 'You are now logged in as ' . $user->first_name . ' ' . $user->last_name . '.', 'success', 'Okay');
        //Redirect to Dashboard page
        if (!deniedPermission('agent_portal')) {
            return redirect()->intended('dashboard');
        } else {
            return redirect()->intended('/helpdesk/dashboard');
        }
    }

    public function syncUsersWithTeam(Request $request)
    {
        $team = Team::where('id', $request->id)->firstOrFail();
        $team->syncUsers(collect($request->agentsToAdd)->lists('id')->toArray());
        return $team;
    }

    public function removeUserFromTeam(Request $request)
    {
        $user = User::where('id', $request->userId)->firstOrFail();
        $team = Team::where('id', $request->teamId)->firstOrFail();

        $user->teams()->detach($team);

        flash()->success(null, 'User Removed.');
        return redirect()->back();
    }

    public function showFormBuilder(Request $request)
    {
        $data = [
                'categories' => Category::with(['subcategories' => function ($query) {
                    $query->where('active', 1);
                }])->where('active', 1)->orderBy('name')->get(),
                'urgencies' => Urgency::all(),
                'users' => User::where('active', 1)->where('id', '!=', Auth::user()->id)->orderBy('first_name')->get(),
          ];
        return view('app.admin.form-builder', $data);
    }

    public function saveFormBuilder(UpdateUserFormRequest $request)
    {
        $form = $this->createForm($request, false);

        flash()->confirm('Form Created Successfully', 'Your form has been created. Share the following url with people who need to access this form. \n\n' . $form->url, $level = 'success', $buttonText = 'Okay');
        return $form;
    }

    public function showUserForm(Request $request)
    {
        $form = UserForm::with('fields')->where('active', 1)->where('slug', $request->slug)->firstOrFail();
        $formFields = [];

        foreach ($form->fields as $field) {
            $formFields[$field['name']] = ($field['type'] == 'hidden' ? $field['default_value'] : '');
        }

        $data = [
                'form' => $form,
                'formFields' => $formFields
          ];


        return view('app.portal.show-form', $data);
    }

    public function showForms(Request $request)
    {
        //Owned Forms
        $forms = UserForm::with(['owner','last_modified'])->where('owner_id', Auth::user()->id)->orderBy('name')->get();
        //Shared Forms
        if (!Auth::user()->forms()->get()->isEmpty()) {
            foreach (Auth::user()->forms()->with(['owner','last_modified'])->get() as $form) {
                $forms->push($form);
            }
        }
        // dd($forms);
        $data = [
                'forms' => $forms,
                'currentUser' => Auth::user(),
          ];

        return view('app.admin.show-forms', $data);
    }

    public function editForm(Request $request)
    {
        $form = UserForm::with(['fields', 'share_with' => function ($query) {
            $query->select('first_name', 'last_name', 'id');
        }])->where('id', $request->id)->firstOrFail();
        $data = [
                'form' => $form,
                'categories' => Category::with('subcategories')->where('active', 1)->orderBy('name')->get(),
                'urgencies' => Urgency::all(),
                'users' => User::where('active', 1)->where('id', '!=', $form->owner_id)->orderBy('first_name')->get(['first_name', 'last_name', 'id']),
          ];
        //  Post::with(['user'=>function($query){
        //     $query->select('id','username');
        // }])->get();
        // dd($data['form']);
        return view('app.admin.edit-form', $data);
    }

    public function updateForm(UpdateUserFormRequest $request, UserForm $form, FormColumn $columns)
    {
        $index = 1;
        $slug = str_slug($request['name']);
        while (UserForm::where('slug', $slug)->where('id', '!=', $request->id)->exists()) {
            $slug = str_slug($request['name']) . '-' . $index++;
        }

        $form = $form->where('id', $request->id)->first();

        // update the form
        $form->update([
                  'name' => $request['name'],
                  'url' => url()->to('/') . '/forms/' .  $slug,
                  'slug' => $slug,
                  'subcategory_id' => $request['subcategory_id'],
                  'urgency' => $request['urgency'],
                  'active' => 1,
                  'last_modified_by' => Auth::user()->id,
                  'updated_at' => Carbon::now()
          ]);

        $shared_with = [];
        foreach ($request->share_with as $user) {
            array_push($shared_with, $user['id']);
        }
        // if(Auth::user()->id != $form->owner_id) {
        //      array_push($shared_with, Auth::user()->id);
        // }

        if (isset($shared_with)) {
            $form->where('id', $request->id)->first()->users()->sync($shared_with);
        } else {
            $form->users()->sync([]);
        }

        $columns->where('form_id', $request->id)->delete();

        foreach ($request['fields'] as $field) {
            //if the type is select remove any blank options so for example. Option1, , Option2, , ,,
            if ($field['type'] == 'select') {
                $selectArray = explode(',', preg_replace('/\s*,\s*/', ',', $field['default_value']));
                $field['default_value'] = $selectArray;
                foreach ($field['default_value'] as $key => $value) {
                    if ($value == '') {
                        unset($field['default_value'][$key]);
                    }
                }
                $field['default_value'] = implode(',', $field['default_value']);
            }

            //Save all columns for the form
            $columns->create([
                    'form_id' => $request->id,
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'is_required' => ($field['is_required'] ? 1 : 0),
                    'ticket_subject' => (isset($field['ticket_subject_text']) == $field['name'] && $field['ticket_subject'] == true ? 1 : 0),
                    'ticket_description' => (isset($field['ticket_description_text']) == $field['name'] && $field['ticket_description'] == true ? 1 : 0),
                    'default_value' => $field['default_value']
            ]);
        }

        return UserForm::with(['fields', 'share_with'])->where('id', $request->id)->first();
    }

    public function toggleFormStatus(Request $request)
    {
        $form = UserForm::where('id', $request->id)->firstOrFail();

        $form->active = !$form->active;
        $form->save();

        return ['status' => $form->fresh()->active];
    }

    public function removeForm(Request $request)
    {
        $form = UserForm::where('id', $request->id)->firstOrFail();

        $form->delete();

        return ['message' => 'Form removed Successfully', 'form' => $form->toJson()];
    }

    public function copyForm(UpdateUserFormRequest $request)
    {
        $form = $this->createForm($request, true);

        return $form;
    }

    public function createForm($request, $isCopy)
    {
        $index = 1;
        $slug = str_slug($request['name']);
        while (UserForm::where('slug', $slug)->exists()) {
            $slug = str_slug($request['name']) . '-' . $index++;
        }

        // Create the form
        $form = UserForm::create([
                  'name' => $request['name'],
                  'url' => url()->to('/') . '/forms/' .  $slug,
                  'slug' => $slug,
                  'subcategory_id' => $request['subcategory_id'],
                  'urgency' => $request['urgency'],
                  'active' => 1,
                  'last_modified_by' => Auth::user()->id,
                  'owner_id' => Auth::user()->id
          ]);

        $shared_with = $request->share_with;

        if (isset($shared_with)) {
            if ($isCopy) {
                $userIds = [];
                foreach ($request->share_with as $user) {
                    if ($user['id'] != Auth::user()->id) {
                        array_push($userIds, $user['id']);
                    }
                }
                $form->users()->sync($userIds);
            } else {
                $form->users()->sync($shared_with);
            }
        } else {
            $form->users()->sync([]);
        }

        //Save all columns for the form
        foreach ($request['fields'] as $field) {
            FormColumn::create([
                    'form_id' => $form->id,
                    'name' => $field['name'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'is_required' => ($field['is_required'] == 'on' ? 1 : 0),
                    'ticket_subject' => ($field['ticket_subject'] == 'on' ? 1 : 0),
                    'ticket_description' => ($field['ticket_description'] == 'on' ? 1 : 0),
                    'default_value' => $field['default_value']
            ]);
        }

        return $form;
    }

    public function getSlug(Request $request)
    {
        return ['slug' => str_slug($request->field, '_')];
    }
}
