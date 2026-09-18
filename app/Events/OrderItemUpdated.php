<?php

namespace App\Events;

use App\Models\OrderItem;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderItemUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $item;

    public function __construct(OrderItem $item)
    {
        $this->item = $item;
    }

    public function broadcastOn(): array
    {
        // Broadcast to a specific table session channel
        return [
            new Channel('session.' . $this->item->order->order_session_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'order.item.updated';
    }
}
