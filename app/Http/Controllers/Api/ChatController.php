<?php

namespace App\Http\Controllers\Api;

use App\Events\GroupChatMessage;
use App\Events\MessageSent;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\Message;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;



class ChatController extends Controller
{
    public function startChat(Request $request)
    {
        $request->validate([
            'announcement_id' => 'required|exists:announcements,id',
        ]);

        $announcementId = $request->announcement_id;
        $userId = $request->user()->id;

        $conversation = Conversation::where('announcement_id', $announcementId)
            ->where('user_id', $userId)
            ->first();

        if (!$conversation) {
            $conversation = new Conversation();
            $conversation->announcement_id = $announcementId;
            $conversation->user_id = $userId;
            $conversation->save();
        }

        return response()->json([
            'conversation_id' => $conversation->id,
        ]);
    }

    public function getMessages($conversationId)
    {
        $conversation = Conversation::findOrFail($conversationId);
    
        $messages = $conversation->messages;
    
        // Zaktualizuj flagi is_delivered i is_read dla wyświetlonych wiadomości
        $messagesToUpdate = [];
        foreach ($messages as $message) {
            if (!$message->is_delivered || !$message->is_read) {
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
    
        return response()->json([
            'messages' => $messages,
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
            ->update(['is_delivered' => true]);
    
        if ($message) {
            return response()->json(['message' => 'Message marked as delivered']);
        } else {
            return response()->json(['message' => 'Failed to mark message as delivered'], 400);
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
            ->update(['is_read' => true, 'is_delivered' => true]);
    
        if ($message) {
            return response()->json(['message' => 'Message marked as read']);
        } else {
            return response()->json(['message' => 'Failed to mark message as read'], 400);
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

        $conversationsData = $conversations->map(function ($conversation) {
            $firstImage = $conversation->announcement->images->first();
            $imageUrl = $firstImage ? URL::to('/') . Storage::url($firstImage->image_path) : null;

            return [
                'id' => $conversation->id,
                'announcement_title' => $conversation->announcement->title,
                'announcement_first_image' => $imageUrl,
                'latest_message' => $conversation->messages->last(),
            ];
        });

        return response()->json([
            'conversations' => $conversationsData,
        ]);
    }


}