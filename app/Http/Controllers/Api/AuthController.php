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
use App\Models\UserActivationCode;
use App\Http\Resources\UserAnnouncementsStats;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use App\Mail\UserVerification;



class AuthController extends Controller
{

    public function signup(SignupRequest $request)
    {
        $credentials = $request->validated();
    
        $user = User::create([
            'login' => $credentials['login'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);
    
        if ($user) {
            try {
                $verification_code = Str::random(60); // KOD DO FRONTU
                $activation_code = Str::random(60); // KOD DO AKTYWACJI KONTA
                $userActivationCode = UserActivationCode::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'verification_code' => $verification_code,
                    'is_active' => true,
                    'activation_code' => $activation_code,
                ]);
    
                Mail::mailer('smtp')->to($user->email)->send(new UserVerification($user, $activation_code));
    
                return response()->json([
                    'status' => 200,
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'message' => trans('messages.account_created'),
                    'verification_code' => $userActivationCode->verification_code, // Dodaj nowy klucz do odpowiedzi
                ], 200);
            } catch (\Exception $err) {
                return response()->json([
                    'error' => trans('messages.account_created_failed'),
                ], 500);
            }
        } else {
            return response()->json([
                'error' => trans('messages.account_created_failed'),
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
        $activationCode = $request->input('activation_code');

        if ($activationCode === null) {
            return response()->json([
                'status' => 400,
                'error' => trans('messages.uncorrect_activation_code'),
            ], 400);
        }

        $userActivationCode = UserActivationCode::where('activation_code', $activationCode)->first();

        if (!$userActivationCode) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.uncorrect_activation_code'),
            ], 404);
        }

        if (!$userActivationCode->is_active) {
            return response()->json([
                'status' => 400,
                'error' => trans('messages.activation_code_not_active'),
            ], 400);
        }

        // Znajdź użytkownika na podstawie user_id
        $user = User::find($userActivationCode->user_id);

        if (!$user) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.user_not_found'),
            ], 404);
        }

        $user->email_verified_at = now();
        $user->save();

        $userActivationCode->is_active = false;
        $userActivationCode->save();

        // Zaaktualizuj pozostałe rekordy dla tego użytkownika w tabeli users_activation_codes
        UserActivationCode::where('user_id', $userActivationCode->user_id)
            ->where('id', '!=', $userActivationCode->id)
            ->update(['is_active' => false]);

        return response()->json([
            'status' => 200,
            'message' => trans('messages.account_activation_success'),
        ], 200);
    }


    public function checkValidationCode(Request $request) 
    {
        $validationCode = $request->input('validation_code');

        $userActivationCode = UserActivationCode::where('verification_code', $validationCode)->latest()->first();
        $user = $userActivationCode->user;


        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.account_already_active'),
            ], 404);
        }


        if (!$userActivationCode) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.uncorrect_activation_code'),
            ], 404);
        }
        
        

    
        return response()->json([
            'status' => 200,
            'message' => trans('messages.validation_code_valid'),
            'email' => $user->email,
        ], 200);

    }


    public function resendVerificationEmail(Request $request)
    {

        $validationCode = $request->input('validation_code');

        $userValidationCode = UserActivationCode::where('verification_code', $validationCode)->latest()->first();
        $user = $userValidationCode->user;


        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.account_already_active'),
            ], 404);
        }


        if (!$userValidationCode) {
            return response()->json([
                'status' => 404,
                'error' => trans('messages.uncorrect_activation_code'),
            ], 404);
        }
        

        if ($user) {
            try {
                $activation_code = Str::random(60); // KOD DO AKTYWACJI KONTA
                $newUserActivationCode = UserActivationCode::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'verification_code' => $validationCode,
                    'is_active' => true,
                    'activation_code' => $activation_code,
                ]);
    
                Mail::mailer('smtp')->to($user->email)->send(new UserVerification($user, $activation_code));

                $userValidationCode->is_active = false;
                $userValidationCode->save();
    
                return response()->json([
                    'status' => 200,
                    'message' => trans('messages.validation_code_valid'),
                ], 200);

            } catch (\Exception $err) {
                return response()->json([
                    'error' => trans('messages.uncorrect_activation_code'),
                ], 500);
            }
        }else {
            return response()->json([
                'error' => trans('messages.uncorrect_activation_code'),
            ], 500);
        }
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

