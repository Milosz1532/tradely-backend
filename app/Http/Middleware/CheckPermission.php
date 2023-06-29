<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $permissionName)
    {
        $user = $request->user();
        // dd($user);
        if (!$user->hasPermission($permissionName)) {
            return response()->json(['message' => 'Brak wymaganych uprawnień.'], 403);
        }
    
        return $next($request);
    }
    
}
