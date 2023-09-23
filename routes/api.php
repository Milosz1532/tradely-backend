<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AnnouncementController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\KeywordSuggestionController;
use App\Http\Controllers\Api\FiltersController;



use Illuminate\Broadcasting\BroadcastController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

use App\Http\Middleware\ThrottleSuggestions;




Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']); 

    Route::get('/profile/data', [AuthController::class, 'profileData']);
    Route::put('/profile/update/personal', [AuthController::class, 'updatePersonalData']);

    Route::get('/profile/active-announcements', [AnnouncementController::class, 'getUserActiveAnnouncements']);
    Route::get('/profile/completed-announcements', [AnnouncementController::class, 'getUserCompletedAnnouncements']);

    


    
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


Route::middleware('auth:sanctum')->get('/verify_token', [AuthController::class, 'verifyUser']);

Route::middleware('auth:sanctum')->post('/checkPermission', [AuthController::class, 'checkPermission']);






Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/activate-account', [AuthController::class, 'activateAccount']);
Route::post('/check-verification-code',[AuthController::class, 'checkValidationCode']);
Route::post('resend-verification-email',  [AuthController::class, 'resendVerificationEmail']);



Route::get('/subcategoriesFilters/{subcategoryId}/{context}', [FiltersController::class, 'getFiltersForSubcategory']);


Route::get('/announcements/search', [AnnouncementController::class, 'search'])->name('search_announcement');
Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
Route::get('/announcements', [AnnouncementController::class, 'index']);
// Route::get('/announcements', [AnnouncementController::class, 'index'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/announcements', [AnnouncementController::class, 'store']);


Route::get('/categories', [CategoryController::class, 'index']);



// CHAT

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/broadcasting/auth', [BroadcastController::class, 'authenticate']);
});


Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('chat')->group(function () {
        Route::post('start', [ChatController::class, 'startChat']);

        Route::get('newConversation/data/{announcement_id}', [ChatController::class, 'newConversationData']);

        Route::get('conversations', [ChatController::class, 'getConversations']);
        Route::get('conversations/{conversationId}/messages', [ChatController::class, 'getMessages']);
        Route::post('messages', [ChatController::class, 'sendMessage']);

        Route::put('messages/{message_id}/delivered', [ChatController::class, 'markMessageAsDelivered']);
        Route::put('messages/{message_id}/read', [ChatController::class, 'markMessageAsRead']);
    });
});


// Suggestions

Route::middleware('throttle:suggestions')->get('/suggestions', [KeywordSuggestionController::class, 'getSuggestions']);
