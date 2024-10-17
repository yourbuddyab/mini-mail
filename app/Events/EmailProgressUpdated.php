<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EmailProgressUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $campaignId;
    public $processedEmails;
    public $totalEmails;
    /**
     * Create a new event instance.
     */
    public function __construct($campaignId, $processedEmails, $totalEmails)
    {
        $this->campaignId = $campaignId;
        $this->processedEmails = $processedEmails;
        $this->totalEmails = $totalEmails;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('campaign'.$this->campaignId),
        ];
    }
}
