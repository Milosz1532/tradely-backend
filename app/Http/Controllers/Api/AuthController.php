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

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Mail\UserVerification;



class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $credentials = $request->validated();
        // if (!Auth::attempt($credentials)) {
        //     return response([
        //         'error' => trans('validation.invalid_credentials'),
        //     ], 422);
        // }
        


        $user = User::create([
            'login' => $credentials['login'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);
    
        if ($user) {
            try {
                $verificationToken = Str::random(60); // Generuj losowy token weryfikacyjny
                $user->verification_token = $verificationToken;
                $user->save();
    
                Mail::mailer('smtp')->to($user->email)->send(new UserVerification($user));
    
                return response()->json([
                    'status' => 200,
                    'message' => trans('messages.account_created'),
                ], 200);
            } catch (\Exception $err) {
                return response()->json([
                    'error' => trans('messages.account_created_faild'),
                ], 500);
            }
        } else {
            return response()->json([
                'error' => trans('messages.account_created_faild'),
            ], 500);
        }
    }
    
    



    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'error' => trans('validation.invalid_credentials'),
            ], 422);
        }
        
        $user = Auth::user();
        
        if (!$user->hasVerifiedEmail()) {
            return response([
                'error' => trans('validation.verify_email'),
            ], 422);
        }
        
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
    

    public function activateAccount(Request $request)
    {
        $token = $request->input('token');
        
        if ($token === null) {
            return response()->json([
                'status' => 400,
                'error' => trans('messages.uncorrect_email_token'),
                
            ], 400);
        }
        
        $user = User::where('verification_token', $token)->first();
        
        if (!$user) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.uncorrect_email_token'),
            ], 404);
        }
        
        if ($user->email_verified_at !== null) {
            return response()->json([
                'status' => 400,
                'error' => trans('messages.account_already_active'),
            ], 400);
        }
        
        $user->email_verified_at = now();
        $user->verification_token = null;
        $user->save();
        
        return response()->json([
            'status' => 200,
            'message' => trans('messages.account_email_success_verify'),
        ], 200);
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
}

