<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Conversation;
use App\Models\Message;



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

        $conversationId = $request->conversation_id;
        $userId = $request->user()->id;
        $content = $request->content;

        $message = new Message();
        $message->conversation_id = $conversationId;
        $message->user_id = $userId;
        $message->content = $content;
        $message->save();

        return response()->json([
            'message_id' => $message->id,
        ]);
    }

    public function getConversations(Request $request)
    {
        $userId = $request->user()->id;

        $conversations = Conversation::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereHas('announcement', function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                });
        })->get();

        return response()->json([
            'conversations' => $conversations,
        ]);
    }


    // public function getConversations(Request $request)
    // {
    //     $userId = $request->user()->id;

    //     $conversations = Conversation::where('user_id', $userId)->get();

    //     return response()->json([
    //         'conversations' => $conversations,
    //     ]);
    // }


    


}