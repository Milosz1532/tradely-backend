<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use Cache;
use Carbon\Carbon;

class ActivityByUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user('api')) {
            $user = $request->user('api');
            $expiresAt = Carbon::now()->addMinutes(1); // keep online for 1 min
            Cache::put('user-is-online-' . $user->id, true, $expiresAt);
            // last seen
            // User::where('id', $user->id)->update(['last_seen' => (new \DateTime())->format("Y-m-d H:i:s")]);
            $user->last_seen = Carbon::now();
            $user->save();
        }
        return $next($request);
    }
}
