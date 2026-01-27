<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth ;
use Symfony\Component\HttpFoundation\Response;

class roleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,...$roles): Response
    { 
        //? ...$roles to accept multiple roles as parameters from route definition

        //! check if user is authenticated 
        if(Auth::check()){
            //! check if user role is in the allowed roles
            $role= Auth::user()->role ;
            $hasAccess= in_array($role,$roles);

            //! if not, abort with 403
            if(!$hasAccess) {
                abort(403);               
            }

            //? if you logged in by wrong role you will be redirected to abort 403 page
            //? you should clear your session from browser and login again with the correct role
        }
        //! proceed to next middleware/request
        return $next($request);
    }
}
