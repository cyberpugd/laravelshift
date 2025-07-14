<?php

namespace App\Http\Controllers\Portal;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use App\TicketView;
use App\p2helpdesk\Utilities\Timezones;
use App\TicketWorkOrderView;
use App\WorkOrderTemplate;
use App\Http\Requests;
use Auth;
use App\Category;
use App\Subcategory;
use Schema;
use App\Team;
use App\Urgency;
use DB;
use App\Location;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserProfileRequest;

class UsersController extends Controller
{
     protected $timezone;
     public function __construct()
     {
          $this->timezone = new Timezones;
     }
    public function showProfile()
    {
         $data = [
               'user' => Auth::user(),
               'locations' => Location::orderBy('city')->get(),
               'timezones' => $this->timezone->all()
         ];
         return view('app.portal.profile', $data);
    }

    public function updateProfile(UpdateUserProfileRequest $request)
    {
         $user = Auth::user();
         $location = Location::where('id', $request->location)->firstOrFail();
         $user->update([
               'first_name' => $request->first_name,
               'last_name' => $request->last_name,
               'email' => $request->email,
               'location_id' => $request->location,
               'timezone' => $request->timezone,
          ]);
         flash()->success(null, 'Profile Updated.');
         return redirect()->back();
    }
}
