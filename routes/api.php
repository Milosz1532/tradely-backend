<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnnouncementController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;



Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/logout', [AuthController::class, 'logout']); 
    Route::apiResource('/users', UserController::class);
    // Route::get('/users',[UserController::class, 'index']);
});



Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::post('/announcements', [AnnouncementController::class, 'store']);
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);


// Route::middleware('web')->get('/verify_token', function (Request $request) {
//     $token = $request->cookie('ACCESS_TOKEN');
//     dd($token);
//     if ($token) {
//         // Tutaj można dodać dodatkową logikę weryfikacji tokenu
//         // Na przykład, jeśli używasz Sanctum, możesz użyć metody `auth()->user()` do weryfikacji tokenu.

//         return response()->json(['message' => 'Prawidłowy token']);
//     } else {
//         return response()->json(['message' => 'Nieprawidłowy token'], 401);
//     }
// });

Route::middleware('auth:sanctum')->get('/verify_token', function (Request $request) {

    return response(null, 200);

});




//27|8sonA5HS0qQJW9GOwJp7wZPNjAtEvAJDO7P4MDNN


//27%7C8sonA5HS0qQJW9GOwJp7wZPNjAtEvAJDO7P4MDNN

//    "token": "28|FuJfpBoTwoqVGR67xEzw0Dl8Rhv9myEIfxcbGQAa"
//              28%257CFuJfpBoTwoqVGR67xEzw0Dl8Rhv9myEIfxcbGQAa