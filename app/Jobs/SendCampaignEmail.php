<?php

namespace App\Jobs;

use App\Events\EmailProgressUpdated;
use App\Mail\CampaignEmail;
use App\Models\CampaignUser;
use App\Models\ProccesStatus;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $emails;
    protected $campaign;
    protected $totalEmail;
    protected $processedEmails;
    protected $chunkSize;

    /**
     * Create a new job instance.
     */
    public function __construct($emails, $campaign, $totalEmail, $processedEmails, $chunkSize)
    {
        $this->emails = $emails;
        $this->campaign = $campaign;
        $this->totalEmail = $totalEmail;
        $this->processedEmails = $processedEmails;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->emails as $email) {
            try {
                Mail::to($email->email)->send(new CampaignEmail(['name' => $email->name, 'contant' => $this->campaign->contant, 'subject' => $this->campaign->name]));
                CampaignUser::create([
                    'campaign_id' => $this->campaign->id,
                    'user_id' => $email->id,
                    'status' => '1'
                ]);
                Log::debug("send to " . $email->name);
            } catch (Exception $e) {
                CampaignUser::create([
                    'campaign_id' => $this->campaign->id,
                    'user_id' => $email->id,
                    'status' => '0'
                ]);
            }
        }
        ProccesStatus::create([
            'campaign_id' => $this->campaign->id,
            'proccesd' => intval($this->processedEmails) + intval($this->chunkSize),
            'total'    => $this->totalEmail,
            'type'      => '2'
        ]);
        // broadcast(new EmailProgressUpdated($this->campaign->id, $this->processedEmails, $this->totalEmail));
    }
}
