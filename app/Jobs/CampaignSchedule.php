<?php

namespace App\Jobs;

use App\Events\EmailProgressUpdated;
use App\Mail\CampaignEmail;
use App\Models\CampaignUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class CampaignSchedule implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $campaign;

    /**
     * Create a new job instance.
     */
    public function __construct($campaign)
    {
        $this->campaign = $campaign;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $processedEmails = 0;
        $chunkSize = 500;
        $totalEmail = $this->campaign->emails->count();

        $this->campaign->emails()->chunk($chunkSize, function ($emails) use (&$processedEmails, $chunkSize, $totalEmail) {
            SendCampaignEmail::dispatch($emails, $this->campaign, $totalEmail, $processedEmails, $chunkSize);
            $processedEmails += $chunkSize;
        });

        $this->campaign->update(['status' => '2']);
    }
}
