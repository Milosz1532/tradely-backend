<?php

namespace App\Http\Controllers\Api;

use App\Models\User;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;


use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;




class AuthController extends Controller
{
    public function signup(SignupRequest $request) {

        $data = $request->validated();
        $user = User::create([
            'login' => $data['login'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;
        

        return response(compact('user', 'token'));
    }

    // public function signup(Request $request) 
    // {
    //     $data = [
    //         'message' => trans('validation.login_required')
    //       ];
    //       return response()->json($data, 404);
    // }




    public function login(LoginRequest $request) {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'message' => trans('validation.invalid_credentials'),
            ], 422);
        }
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        $cookie = cookie('ACCESS_TOKEN', $token, 60); 
        return response(compact('user', 'token'))->cookie($cookie);
    }


    public function logout(Request $request) {
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response('',204);
    }
}
