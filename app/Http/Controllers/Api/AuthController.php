<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\Announcement;
use App\Http\Resources\UserAnnouncementsStats;



class AuthController extends Controller
{
    public function signup(SignupRequest $request) 
    {
        $data = $request->validated();
        $user = User::create([
            'login' => $data['login'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;
        

        return response(compact('user', 'token'));
    }



    public function profileData(Request $request) 
    {
        $userId = $request->user()->id;
        $user = Auth::user();

        $allAnnouncementsCount = Announcement::where('user_id', $userId)->count();
    
        $activeAnnouncementsCount = Announcement::where('user_id', $userId)
            ->where('status_id', 2)
            ->count();
    
        $favoriteAnnouncementsCount = $user->favoriteAnnouncements()->count();
    
        $latestAnnouncements = Announcement::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();
    
        $data = [
            'all_announcements_count' => $allAnnouncementsCount,
            'active_announcements_count' => $activeAnnouncementsCount,
            'favorite_announcements_count' => $favoriteAnnouncementsCount,
            'latest_announcements' => UserAnnouncementsStats::collection($latestAnnouncements),
        ];

    
        return response()->json($data);
    }


    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'message' => trans('validation.invalid_credentials'),
            ], 422);
        }
        
        $user = Auth::user();
        $permissions = $user->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name,
            ];
        });
        
        $token = $user->createToken('main')->plainTextToken;
        $cookie = cookie('ACCESS_TOKEN', $token, 60);
        
        return response([
            'user' => $user->only([
                'id',
                'login',
                'first_name',
                'last_name',
                'birthday',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
            ]),
            'token' => $token,
            'permissions' => $permissions,
        ])->cookie($cookie);
    }
    
    

    public function logout(Request $request) 
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response('',204);
    }

    public function checkPermission(Request $request)
    {
        $permission = $request->input('permission');

        $user = $request->user();

        

        if ($user->hasPermission($permission)) {
            return response()->json(['has_permission' => true], 200);
        } else {
            return response()->json(['has_permission' => false], 403);
        }
    }
}

