<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class PostUpdated implements shouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $themeId;
    public int $postId;
    public int $likesCount;

    /**
     * Create a new event instance.
     */
    public function __construct($themeId, $postId, $likesCount)
    {
        $this->themeId = $themeId;
        $this->postId = $postId;
        $this->likesCount = $likesCount;
    }

    /**
     * Get the channels the event should broadcast on.
     * ブロードキャストするチャンネルを指定
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('theme.' . $this->themeId),
        ];
    }

    // ブロードキャスト時に送信するデータ
    public function broadcastWith(): array
    {
        return [
            'postId' => $this->postId,
            'likesCount' => $this->likesCount,
        ];
    }
}
