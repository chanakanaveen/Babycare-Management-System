<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if(! $request->expectsJson()){
            if($request->routeIs('moh.*')){
                session()->flash('fail','You must be logged in to access this page');
                return route('moh.login');
            }

            if( $request->routeIs('midwife.*') ){
                session()->flash('fail','You must login first');
                return route('midwife.login');
            }

            if( $request->routeIs('parent.*') ){
                session()->flash('fail','You must login first');
                return route('parent.login');
            }
        }
    }
}
