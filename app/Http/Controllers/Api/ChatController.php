<?php

namespace App\Http\Controllers\Api;

use App\Events\MessageSent;
use App\Events\MessageDelivered;
use App\Events\MessageRead;

use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\Announcement;
use App\Models\Message;
use App\Models\User;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use Cache;
use Carbon\Carbon;


class ChatController extends Controller
{
    public function startChat(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
            'content' => 'required|string',
        ]);

        $announcementId = $request->announcement_id;
        $userId = $request->user()->id;
        $content = $request->content;

        $conversation = Conversation::where('announcement_id', $announcementId)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->announcement_id = $announcementId;
            $conversation->user_id = $userId;
            $conversation->save();
        }

        $message = new Message();
        $message->conversation_id = $conversation->id;
        $message->user_id = $userId;
        $message->content = $content;
        $message->save();

        $eventMessage = [
            'id' => $message->id,
            'conversation_id' => $conversation->id,
            'content' => $content,
            'created_at' => $message->created_at,
            'user_id' => $userId,
        ];

        $firstImage = $conversation->announcement->images->first();
        $imageUrl = $firstImage ? URL::to('/') . Storage::url($firstImage->image_path) : null;

        $conversationData = [
            'id' => $conversation->id,
            'announcement_title' => $conversation->announcement->title,
            'announcement_first_image' => $imageUrl,
            'latest_message' => $conversation->messages->last(),
        ];


        $announcement_owner_id = $conversation->Announcement->user_id;
        $conversation_user_id = $conversation->user_id;
        $recipient_id = $announcement_owner_id == $userId ? $conversation_user_id : $announcement_owner_id;
        

        event(new MessageSent($eventMessage, $recipient_id));


        return response()->json(['conversation' => $conversationData, 'message' => $eventMessage]);
    }



    
    public function getMessages(Request $request, $conversationId)
    {
        $request->merge(['conversationId' => $conversationId]);
    
        $request->validate([
            'conversationId' => 'required|exists:conversations,id',
        ]);
    
        $conversation = Conversation::findOrFail($conversationId);
        $messages = $conversation->messages;
    
        // Zaktualizuj flagi is_delivered i is_read dla wyświetlonych wiadomości
        $messagesToUpdate = [];
        foreach ($messages as $message) {
            if (($message->user_id !== $request->user()->id) && (!$message->is_delivered || !$message->is_read)) {
                $message->is_delivered = true;
                $message->is_read = true;
                $messagesToUpdate[] = $message;
            }
        }
    
        if (!empty($messagesToUpdate)) {
            DB::transaction(function () use ($messagesToUpdate) {
                foreach ($messagesToUpdate as $message) {
                    $message->save();
                }
            });
        }
        $user_id = $request->user()->id;
    
        $announcement_owner_id = $conversation->Announcement->user_id;
        $conversation_user_id = $conversation->user_id;
        $recipient_id = $announcement_owner_id == $user_id ? $conversation_user_id : $announcement_owner_id;
        event(new MessageRead($recipient_id, 'all', $conversationId));
    
        $formattedMessages = $messages->map(function ($message) {
            $status = '';
            if ($message->is_read) {
                $status = 3;
            } elseif ($message->is_delivered) {
                $status = 2;
            } elseif ($message->is_sent) {
                $status = 1;
            }
    
            return [
                'id' => $message->id,
                'conversation_id' => $message->conversation_id,
                'user_id' => $message->user_id,
                'content' => $message->content,
                'created_at' => $message->created_at,
                'status' => $status,
            ];
        });
    
        return response()->json([
            'messages' => $formattedMessages,
        ]);
    }
    
    
    

    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string',
        ]);


        $conversation_id = $request->conversation_id;

        $conversation = Conversation::findOrFail($conversation_id);
        if ($conversation) {
            $user = $request->user();
            $user_id = $request->user()->id;


            $announcement_owner_id = $conversation->Announcement->user_id;
            $conversation_user_id = $conversation->user_id;
            $recipient_id = $announcement_owner_id == $user_id ? $conversation_user_id : $announcement_owner_id;
            

            $content = $request->content;

            $message = new Message();
            $message->conversation_id = $conversation_id;
            $message->user_id = $user_id;
            $message->content = $content;
            $message->is_sent = true;
            $message->save();

            $eventMessage = [
                'id' => $message->id,
                'conversation_id' => $conversation_id,
                'content' => $content,
                'created_at' => $message->created_at,
                'user_id' => $user_id,
            ];
            
            event(new MessageSent($eventMessage, $recipient_id));
    
            return response()->json($eventMessage);
        }
    }

    public function markMessageAsDelivered(Request $request, $message_id)
    {
        $request->merge(['message_id' => $message_id]);


        $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);
    
        $message = Message::where('id', $message_id)
        ->where('is_delivered', false)
        ->first();

        if ($message) {
            $message->is_delivered = true;
            $message->save();

            $conversation_id = $message->conversation_id;

            $conversation = Conversation::findOrFail($conversation_id);
            if ($conversation) {
                $user = $request->user();
                $user_id = $request->user()->id;
    
    
                $announcement_owner_id = $conversation->Announcement->user_id;
                $conversation_user_id = $conversation->user_id;
                $recipient_id = $announcement_owner_id == $user_id ? $conversation_user_id : $announcement_owner_id;


                event(new MessageDelivered($recipient_id, $message->id, $conversation_id));
            }   
        }
    }
    
    

    public function markMessageAsRead(Request $request, $message_id)
    {
        $request->merge(['message_id' => $message_id]);


        $request->validate([
            'message_id' => 'required|exists:messages,id',
        ]);
    
        $message = Message::where('id', $message_id)
        ->where('is_read', false)
        ->first();

        if ($message) {
            $message->is_delivered = true;
            $message->is_read = true;
            $message->save();

            $conversation_id = $message->conversation_id;

            $conversation = Conversation::findOrFail($conversation_id);
            if ($conversation) {
                $user = $request->user();
                $user_id = $request->user()->id;
    
    
                $announcement_owner_id = $conversation->Announcement->user_id;
                $conversation_user_id = $conversation->user_id;
                $recipient_id = $announcement_owner_id == $user_id ? $conversation_user_id : $announcement_owner_id;


                event(new MessageRead($recipient_id, $message->id, $conversation_id));
            }   
        }

    }
    

    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;
    
        $conversations = Conversation::with(['announcement', 'messages' => function ($query) {
            $query->latest()->take(1);
        }])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhereHas('announcement', function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    });
            })
            ->get();
    
        $conversationsData = $conversations->map(function ($conversation) use ($userId) {
            $firstImage = $conversation->announcement->images->first();
            $imageUrl = $firstImage ? URL::to('/') . Storage::url($firstImage->image_path) : null;
    
            $announcement_owner_id = $conversation->Announcement->user_id;
            $conversation_user_id = $conversation->user_id;
            $recipient_id = $announcement_owner_id == $userId ? $conversation_user_id : $announcement_owner_id;
    
            $user_last_activity = false;
    
            if (Cache::has('user-is-online-' . $recipient_id)) {
                $user_last_activity = true;
            } else {
                $recipient = User::findOrFail($recipient_id);
                $user_last_activity = Carbon::parse($recipient->last_seen)->diffForHumans();
            }
    
            return [
                'id' => $conversation->id,
                'announcement_title' => $conversation->announcement->title,
                'announcement_first_image' => $imageUrl,
                'latest_message' => $conversation->messages->last(),
                'user_last_activity' => $user_last_activity,
            ];
        });
    
        return response()->json([
            'conversations' => $conversationsData,
        ]);
    }
    

    public function newConversationData(Request $request, $announcement_id)
    {
        $request->merge(['announcement_id' => $announcement_id]);
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
        ]);

        $user = $request->user();
        $userId = $user->id;

        $announcement = Announcement::findOrFail($announcement_id);
        if ($announcement->user_id === $user->id) {
            return response()->json(['error' => 'Nie możesz rozpocząć konwersacji ze swoim własnym ogłoszeniem.', 'status' => 0]);
        }

        // Sprawdzenie, czy użytkownik nie prowadzi już konwersacji z tym ogłoszeniem
        $existingConversation = Conversation::where('announcement_id', $announcement_id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingConversation) {
            $firstImage = $existingConversation->announcement->images->first();
            $imageUrl = $firstImage ? URL::to('/') . Storage::url($firstImage->image_path) : null;

            // return [
            //     'id' => $existingConversation->id,
            //     'announcement_title' => $existingConversation->announcement->title,
            //     'announcement_first_image' => $imageUrl,
            //     'latest_message' => $existingConversation->messages->last(),
            // ];
            return response()->json([
                'id' => $existingConversation->id,
                'announcement_title' => $existingConversation->announcement->title,
                'announcement_first_image' => $imageUrl,
                'latest_message' => $existingConversation->messages->last(),
            ],202);
            // return response()->json(['error' => 'Już prowadzisz konwersację z tym ogłoszeniem.', 'status' => 1]);
        }
        

        $formattedAnnouncement = [
            'id' => $announcement->id,
            'title' => $announcement->title,
            'description' => $announcement->description,
            // ... inne dane ogłoszenia ...
        ];

        $formattedUser = [
            'id' => $announcement->user->id,
            'name' => $announcement->user->name,
            'email' => $announcement->user->email,
            // ... inne dane użytkownika ...
        ];

        return response()->json([
            'announcement' => $formattedAnnouncement,
            'user' => $formattedUser,
        ]);


        // Zwróc tylko dane ogłoszenia i uzytkownika

    }


}