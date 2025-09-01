<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\EmailLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 минут
    public $tries = 3;

    protected $campaign;

    public function __construct(EmailCampaign $campaign)
    {
        $this->campaign = $campaign;
    }

    public function handle(): void
    {
        try {
            Log::info("Starting campaign {$this->campaign->id} with {$this->campaign->recipients_count} recipients");
            
            // Рассчитываем количество пакетов
            $totalBatches = ceil($this->campaign->recipients_count / $this->campaign->batch_size);
            
            // Обновляем кампанию
            $this->campaign->update([
                'status' => 'active',
                'sent_at' => now(),
                'total_batches' => $totalBatches,
                'current_batch' => 0,
                'next_batch_at' => now()
            ]);
            
            // Запускаем первый пакет немедленно
            ProcessEmailBatch::dispatch($this->campaign, 1);
            
            Log::info("Campaign {$this->campaign->id} started with {$totalBatches} batches");
            
        } catch (\Exception $e) {
            Log::error('Campaign startup failed: ' . $e->getMessage(), [
                'campaign_id' => $this->campaign->id
            ]);
            
            $this->campaign->update(['status' => 'failed']);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Campaign startup permanently failed: ' . $exception->getMessage(), [
            'campaign_id' => $this->campaign->id
        ]);
        
        $this->campaign->update(['status' => 'failed']);
    }
}
