<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Auth;
use App\Models\Conversation;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::routes(['middleware' => 'auth:sanctum']);


// Broadcast::channel('messanger_chat.{id}', function ($user, $conversationId) {
//     $conversation = Conversation::find($conversationId);

//     if ($conversation) {
//         // Sprawdzenie, czy użytkownik jest właścicielem ogłoszenia
//         if ($user->id === $conversation->announcement->user_id) {
//             return ['id' => $user->id, 'name' => $user->email];
//         }

//         // Sprawdzenie, czy użytkownik rozpoczął konwersację
//         if ($user->id === $conversation->user_id) {
//             return ['id' => $user->id, 'name' => $user->email];
//         }
//     }

//     return false;
// });

Broadcast::channel('messanger_user.{userId}', function ($user, $userId) {
    if ($user->id == $userId) {
        // return ['id' => $user->id, 'name' => $user->email];
        return "Jest gicio";
    }

    return false;
});



