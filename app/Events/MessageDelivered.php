<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageDelivered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user_id;
    public $messageId;
    public $conversationId;

    public function __construct($user_id, $messageId, $conversationId)
    {
        $this->user_id = $user_id;
        $this->messageId = $messageId;
        $this->conversationId = $conversationId;
    }


    public function broadcastOn()
    {
        return new PrivateChannel('messanger_user.' . $this->user_id);
    }
}
