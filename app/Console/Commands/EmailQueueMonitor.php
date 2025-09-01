<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Jobs\ProcessEmailBatch;
use Carbon\Carbon;

class EmailQueueMonitor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:monitor 
                            {action=status : Action to perform (status|resume|pause|stats)}
                            {--campaign= : Specific campaign ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor and manage email campaign queues';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');
        $campaignId = $this->option('campaign');

        switch ($action) {
            case 'status':
                return $this->showStatus($campaignId);
            case 'resume':
                return $this->resumeCampaigns($campaignId);
            case 'pause':
                return $this->pauseCampaigns($campaignId);
            case 'stats':
                return $this->showStats($campaignId);
            default:
                $this->error("Unknown action: {$action}");
                return 1;
        }
    }

    private function showStatus($campaignId = null): int
    {
        $query = EmailCampaign::query();
        
        if ($campaignId) {
            $query->where('id', $campaignId);
        } else {
            $query->whereIn('status', ['active', 'paused']);
        }
        
        $campaigns = $query->with('logs')->get();
        
        if ($campaigns->isEmpty()) {
            $this->info('No active campaigns found.');
            return 0;
        }
        
        $headers = ['ID', 'Name', 'Status', 'Progress', 'Current Batch', 'Next Batch At', 'Rate'];
        $rows = [];
        
        foreach ($campaigns as $campaign) {
            $progress = $campaign->recipients_count > 0 
                ? round(($campaign->sent_count + $campaign->failed_count) / $campaign->recipients_count * 100, 1) 
                : 0;
                
            $rate = $campaign->emails_per_minute ?? 0;
            $sentAndFailed = $campaign->sent_count + $campaign->failed_count;
            
            $rows[] = [
                $campaign->id,
                substr($campaign->name, 0, 20),
                $campaign->status,
                $progress . '% (' . $sentAndFailed . '/' . $campaign->recipients_count . ')',
                $campaign->current_batch . '/' . $campaign->total_batches,
                $campaign->next_batch_at ? $campaign->next_batch_at->format('H:i:s') : '-',
                $rate . '/min'
            ];
        }
        
        $this->table($headers, $rows);
        return 0;
    }
    
    private function resumeCampaigns($campaignId = null): int
    {
        $query = EmailCampaign::where('status', 'paused');
        
        if ($campaignId) {
            $query->where('id', $campaignId);
        }
        
        $campaigns = $query->get();
        
        foreach ($campaigns as $campaign) {
            $campaign->update(['status' => 'active']);
            
            // Проверяем, есть ли незавершенные пакеты
            $remainingEmails = EmailLog::where('campaign_id', $campaign->id)
                ->where('status', 'pending')
                ->count();
                
            if ($remainingEmails > 0) {
                // Запускаем следующий пакет
                $nextBatch = $campaign->current_batch + 1;
                ProcessEmailBatch::dispatch($campaign, $nextBatch);
                
                $this->info("Resumed campaign {$campaign->id}: {$campaign->name}");
            } else {
                $campaign->update(['status' => 'completed']);
                $this->info("Campaign {$campaign->id} was already completed");
            }
        }
        
        return 0;
    }
    
    private function pauseCampaigns($campaignId = null): int
    {
        $query = EmailCampaign::where('status', 'active');
        
        if ($campaignId) {
            $query->where('id', $campaignId);
        }
        
        $campaigns = $query->get();
        
        foreach ($campaigns as $campaign) {
            $campaign->update(['status' => 'paused']);
            $this->info("Paused campaign {$campaign->id}: {$campaign->name}");
        }
        
        return 0;
    }
    
    private function showStats($campaignId = null): int
    {
        $query = EmailCampaign::query();
        
        if ($campaignId) {
            $query->where('id', $campaignId);
        }
        
        $campaigns = $query->with('logs')->get();
        
        foreach ($campaigns as $campaign) {
            $this->info("Campaign: {$campaign->name} (ID: {$campaign->id})");
            $this->info("Status: {$campaign->status}");
            $this->info("Recipients: {$campaign->recipients_count}");
            $this->info("Sent: {$campaign->sent_count}");
            $this->info("Failed: {$campaign->failed_count}");
            $this->info("Success Rate: {$campaign->success_rate}%");
            $this->info("Batch Settings: {$campaign->batch_size} emails per batch, {$campaign->delay_between_batches}s delay");
            $this->info("Current Progress: Batch {$campaign->current_batch}/{$campaign->total_batches}");
            
            if ($campaign->next_batch_at) {
                $this->info("Next Batch: {$campaign->next_batch_at}");
            }
            
            $this->line('---');
        }
        
        return 0;
    }
}
