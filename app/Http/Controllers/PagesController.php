<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Exception;
use App\Http\Requests;
use App\User;
use Adldap;
use Auth;
use App\Role;
use App\p2helpdesk\Utilities\Timezones;

class PagesController extends HelpdeskController
{
    public function login() {

    	$data = [
    		'adid' => $_SERVER['AUTH_USER']
    	];

    	return view('layouts.splash', $data);

    }


    public function postLogin(Timezones $timezone) {
	 	
    	//check of a user exists in the database
	   	$check = User::where(['ad_id' => $_SERVER['AUTH_USER']])->get();

	   	//if user doesn't exist, create account for them
          if($check->isEmpty()) {
               //Get the user from AD
			$user = Adldap::getProvider('default')->search()->users()->where('samaccountname', '=', substr($_SERVER['AUTH_USER'], 6))->get();
                                //Get the location id of the user based on the city. If location doesn't exist it will default to Denver.
                                $location =  $timezone->mapToLocation($user[0]['l'][0]);
                                // dd($location);
                                // Create the user in the database
	   		$user = User::create([
	   			'ad_id' => strtolower($_SERVER['AUTH_USER']),
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
	   	} 

	   	//Find the user in the database
	   	try {
	   		$user = User::where(['ad_id' => $_SERVER['AUTH_USER'], 'active' => 1])->firstOrFail();
	   	} catch(Exception $e)
	   	{
	   		flash()->basicWarningStay('You do not have an active account, please contact your administrator.');
	   		return redirect('/');
	   	}

	   	//Login the user to the application
	   	Auth::login($user);

	   	//Redirect to Dashboard page
                    if(!deniedPermission('agent_portal')) {
	   	     return redirect()->intended('dashboard');
                    } else {
                         return redirect()->intended('/helpdesk/dashboard');
                    }
    }


    public function logout(Request $request)
    {
    	Auth::logout();
        return redirect('/');
    }

          public function activeDirectory()
          {
               //Get the user from AD
               $user = Adldap::getProvider('default')->search()->users()->where('samaccountname', '=', substr($_SERVER['AUTH_USER'], 6))->get();
               dd($user);
          }
}
