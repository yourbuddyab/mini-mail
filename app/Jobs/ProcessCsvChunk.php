<?php

namespace App\Jobs;

use App\Events\EmailSaveUpdated;
use App\Models\Email;
use App\Models\ProccesStatus;
use App\Models\ValidationFail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProcessCsvChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $records;
    protected $campaignId;
    protected $processedEmails;
    protected $chunkSize;
    public $timeout = 1800;
    /**
     * Create a new job instance.
     */
    public function __construct($records, $campaignId, $processedEmails, $chunkSize)
    {
        $this->records = $records;
        $this->campaignId = $campaignId;
        $this->processedEmails = $processedEmails;
        $this->chunkSize = $chunkSize;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emailData = [];

        foreach ($this->records as $record) {
            // Validate each record
            $contactValidator = Validator::make($record, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
            ]);

            if ($contactValidator->fails()) {
                // Log validation failures
                ValidationFail::create([
                    'campaign_id' => $this->campaignId,
                    'content'     => json_encode($record)
                ]);
                continue;
            }

            $emailData[] = [
                'campaign_id' => $this->campaignId,
                'name'        => $record['name'],
                'email'       => $record['email'],
            ];
        }
        if (!empty($emailData)) {
            Email::insert($emailData);
            $this->processedEmails += count($emailData);
        }
        Log::debug(json_encode([
            'campaign_id' => $this->campaignId,
            'proccesd' => intval($this->processedEmails)+intval($this->chunkSize),
            'failed'    => ValidationFail::where('campaign_id', $this->campaignId)->count(),
            'type'      => '1'
        ]));

        $value = ProccesStatus::create([
            'campaign_id' => $this->campaignId,
            'proccesd' => intval($this->processedEmails)+intval($this->chunkSize),
            'failed'    => ValidationFail::where('campaign_id', $this->campaignId)->count(),
            'type'      => '1'
        ]);

        Log::debug(json_encode($value));
        // broadcast(new EmailSaveUpdated($this->campaignId, $this->processedEmails, $failedEmails));
    }
}
