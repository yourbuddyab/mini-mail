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

    public $campaign;

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
        $this->campaign->emails()->chunk(2, function ($emails) use ($processedEmails) {
            foreach ($emails as $email) {
                try {
                    Mail::to($email->email)->send(new CampaignEmail(['name' => $email->name, 'contant' => $this->campaign->contant, 'subject' => $this->campaign->name]));
                    CampaignUser::create([
                        'campaign_id' => $this->campaign->id,
                        'user_id' => $email->id,
                        'status' => '1'
                    ]);
                    $processedEmails++;
                    Log::debug("send to " . $email->name);
                } catch (Exception $e) {
                    dd($e);
                    CampaignUser::create([
                        'campaign_id' => $this->campaign->id,
                        'user_id' => $email->id,
                        'status' => '0'
                    ]);
                }
            }
        });
        $this->campaign->update(['status' => '2']);
    }
}
