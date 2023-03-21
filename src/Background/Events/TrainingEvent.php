<?php

namespace SavvyAI\Events;

use SavvyAI\Models\Property;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrainingEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $propertyId;
    public string $eventName;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(int $propertyId, string $eventName)
    {
        $this->propertyId = $propertyId;
        $this->eventName = $eventName;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel("property.{$this->propertyId}");
    }
}
