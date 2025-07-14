<?php

namespace App\Http\Middleware;
use Auth;
use Closure;
use App\Http\Requests\Request;
class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $permission)
    {
          if(is_array($permission)) {
               foreach($permission as $perm) {
                     if(!deniedPermission($perm)) {
                         return $next($request);
                    }
               }
               flash()->info(null, 'You do not have permission to do that');
                 if(!deniedPermission('agent_portal')) {
                              return redirect('dashboard');
                         } else {
                              return redirect('/helpdesk/dashboard');
                         }
          } else {
               if ($request->isJson())
               {
                    if(deniedPermission($permission)) {
                          return response()->json([
                              'status' => 'Error',
                              'Description' => 'You do not have permission to do that.'
                         ], 401);
                    }
               }
               if(deniedPermission($permission)) {
                    flash()->info(null, 'You do not have permission to do that');
                     if(!deniedPermission('agent_portal')) {
                              return redirect('dashboard');
                         } else {
                              return redirect('/helpdesk/dashboard');
                         }
               }
          }
        return $next($request);
    }
}
