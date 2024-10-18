<?php

namespace App\Jobs;

use App\Events\EmailSaveUpdated;
use App\Models\Campaign;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use League\Csv\Reader;

class ProcessCsv implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $campaign;
    public $timeout = 1800;
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

            $csvData = $csv->getRecords();
            $chunkSize = 500;
            $chunks = array_chunk(iterator_to_array($csvData), $chunkSize);
            $processedEmails = 0;
            foreach ($chunks as $chunk) {
                ProcessCsvChunk::dispatch($chunk, $this->campaign->id, $processedEmails, $chunkSize);
                $processedEmails +=$chunkSize;
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
