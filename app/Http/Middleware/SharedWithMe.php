<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\UserForm;

class SharedWithMe
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
          $form = UserForm::where('id', $request->id)->first();

          if(Auth::user()->id == $form->owner_id) {
               return $next($request);
          }
          if(in_array(Auth::user()->id, $form->share_with->lists('id')->toArray())) {
               return $next($request);
          }

          flash()->info(null, 'You do not have permission to view that form.');
          return redirect('/admin/forms');
          
    }
}
