<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailCampaign;
use App\Jobs\ProcessEmailBatch;
use Carbon\Carbon;

class ProcessScheduledCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'email:process-scheduled';

    /**
     * The console command description.
     */
    protected $description = 'Process scheduled email campaigns';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $scheduledCampaigns = EmailCampaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($scheduledCampaigns->isEmpty()) {
            $this->info('No scheduled campaigns to process.');
            return 0;
        }

        foreach ($scheduledCampaigns as $campaign) {
            try {
                $this->info("Starting scheduled campaign: {$campaign->name} (ID: {$campaign->id})");
                
                // Обновляем статус кампании
                $campaign->update([
                    'status' => 'sending',
                    'started_at' => now()
                ]);
                
                // Запускаем кампанию через batch processor
                ProcessEmailBatch::dispatch($campaign);
                
                $this->info("Campaign {$campaign->id} has been queued for processing");
                
            } catch (\Exception $e) {
                $this->error("Failed to start campaign {$campaign->id}: " . $e->getMessage());
                
                $campaign->update([
                    'status' => 'failed'
                ]);
            }
        }

        $this->info("Processed " . $scheduledCampaigns->count() . " scheduled campaigns");
        return 0;
    }
}
