<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Member;

class AuthenticateAdmin
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
        $member = Member::get();
        if (!$member) {
            return redirect(route('login'));
        }

        if (!$member->is_admin()) {
            abort(404);
        }

        // if ($member->is_admin()) {
        //     dd($member);
        // }

        return $next($request);
    }
}
