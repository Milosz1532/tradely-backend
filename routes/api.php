<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\CategoryController;
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

Route::middleware('auth:sanctum')->get('/verify_token', function (Request $request) {

    $user = $request->user();

    return response()->json($user, 200);


});



Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');


Route::get('/announcements/search', [AnnouncementController::class, 'search'])->name('search_announcement');
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
Route::get('/announcements', [AnnouncementController::class, 'index']);
Route::post('/announcements', [AnnouncementController::class, 'store']);


Route::get('/categories', [CategoryController::class, 'index']);



