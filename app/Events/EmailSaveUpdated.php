<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EmailSaveUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $campaignId;
    public $processedEmails;
    public $failedEmails;
    
    /**
     * Create a new event instance.
     */
    public function __construct($campaignId, $processedEmails, $failedEmails)
    {
        $this->campaignId = $campaignId;
        $this->processedEmails = $processedEmails;
        $this->failedEmails = $failedEmails;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        Log::info('Broadcasting on campaign-updates channel', ['campaignId' => $this->campaignId]);
        return [
            new PrivateChannel('campaignSave'.$this->campaignId),
        ];
    }

    public function broadcastWith()
    {
        return [
            'processedEmails' => $this->processedEmails,
            'failedEmails' => $this->failedEmails,
        ];
    }
}
