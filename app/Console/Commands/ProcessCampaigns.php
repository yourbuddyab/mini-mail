<?php

namespace App\Console\Commands;

use App\Jobs\CampaignSchedule;
use App\Models\Campaign;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-campaigns';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process schedule email campaigns';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $campaigns = Campaign::where('scheduled_at', '<=', Carbon::now())
            ->where('status', '0')
            ->with('emails')
            ->get();
        foreach ($campaigns as $campaign) {
            CampaignSchedule::dispatch($campaign);

            $campaign->update(['status' => '1']);
        }
        $this->info('Pending campaigns processed successfully.');
    }
}
