<?php

namespace App\Jobs;

use App\Events\EmailSaveUpdated;
use App\Models\Campaign;
use App\Models\Email;
use App\Models\ValidationFail;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

class ProcessCsv implements ShouldQueue
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
        try {
            $csv = Reader::createFromPath(storage_path('app/' . $this->campaign->csv_file), 'r');
            $csv->setHeaderOffset(0);

            $header = $csv->getHeader();
            if (!in_array('name', $header) || !in_array('email', $header)) {
                throw new Exception("Invalid CSV data for name: " . ($record['name'] ?? 'unknown'));
            }

            $records = $csv->getRecords();
            foreach ($records as $record) {
                $contactValidator = Validator::make($record, [
                    'name' => 'required|string|max:255',
                    'email' => 'required|email|max:255',
                ]);

                if ($contactValidator->fails()) {
                    ValidationFail::create([
                        'campaign_id' => $this->campaign->id,
                        'content'     => json_encode($record)
                    ]);
                }
                Email::create([
                    'campaign_id' => $this->campaign->id,
                    'name' => $record['name'],
                    'email' => $record['email'],
                ]);
            }
            Campaign::where('id', $this->campaign->id)->update(['status' => 1]);
        } catch (Exception $e) {
            $this->fail($e);
        }
    }

    public function failed(Exception $exception)
    {
        logger()->error('CSV processing failed for campaign: ' . $this->campaign->id, [
            'filePath' => $this->campaign->csv_file,
            'error' => $exception->getMessage(),
        ]);
    }
}
