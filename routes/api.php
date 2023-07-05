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

    Route::get('/profile/data', [AuthController::class, 'profileData']);
    Route::put('/profile/update/personal', [AuthController::class, 'updatePersonalData']);

    Route::get('/profile/announcements', [AnnouncementController::class, 'userAnnouncements']);
    Route::get('/profile/favoriteAnnouncements', [AnnouncementController::class, 'userFavoriteAnnouncements']);
    Route::post('/announcement/like', [AnnouncementController::class, 'likeAnnouncement']);
});


//TEMPLATE
// Route::middleware('auth:sanctum')->group(function () {
//     Route::middleware('auth:sanctum')->get('/get', function (Request $request) {

//         $user = $request->user();
//         $user_permission = $user->permissions;
    
//         return response()->json($user_permission, 200);
//     });
    
//     Route::middleware('permission:ANNOUNCEMENT.EDIT')->get('/get_edit', function () {
//         // Ta trasa zostanie chroniona przez middleware
//         // tylko dla użytkowników posiadających uprawnienie 'ANNOUNCEMENT.EDIT'
//         return response()->json(['message' => 'Posiadasz wymagane uprawnienia.'], 200);
//     });
    
//     Route::middleware('permission:ANNOUNCEMENT.DELETE')->get('/get_Delete', function () {
//         // Ta trasa zostanie chroniona przez middleware
//         // tylko dla użytkowników posiadających uprawnienie 'ANNOUNCEMENT.DELETE'
//         return response()->json(['message' => 'Posiadasz wymagane uprawnienia.'], 200);
//     });
    

// });


Route::middleware('auth:sanctum')->get('/verify_token', function (Request $request) {
    $user = $request->user();
    $permissions = $user->permissions->map(function ($permission) {
        return $permission->only(['id', 'name']);
    });

    return response()->json([
        'user' => $user->only(['id', 'login', 'first_name', 'last_name', 'birthday', 'email', 'email_verified_at', 'created_at', 'updated_at', 'note']),
        'permissions' => $permissions,
    ], 200);
});

Route::middleware('auth:sanctum')->post('/checkPermission', [AuthController::class, 'checkPermission']);






Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/activate-account', [AuthController::class, 'activateAccount']);
Route::post('/check-verification-code',[AuthController::class, 'checkValidationCode']);
Route::post('resend-verification-email',  [AuthController::class, 'resendVerificationEmail']);





Route::get('/announcements/search', [AnnouncementController::class, 'search'])->name('search_announcement');
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
Route::get('/announcements', [AnnouncementController::class, 'index']);
// Route::get('/announcements', [AnnouncementController::class, 'index'])->middleware('auth:sanctum');
Route::post('/announcements', [AnnouncementController::class, 'store']);


Route::get('/categories', [CategoryController::class, 'index']);



