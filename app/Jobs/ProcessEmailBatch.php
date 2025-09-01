<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\EmailLog;
use App\Mail\CampaignEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessEmailBatch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 час
    public $tries = 3;
    
    protected $campaign;
    protected $batchNumber;

    public function __construct(EmailCampaign $campaign, int $batchNumber = null)
    {
        $this->campaign = $campaign;
        $this->batchNumber = $batchNumber ?? ($campaign->current_batch + 1);
        
        // Устанавливаем очередь с приоритетом для больших рассылок
        if ($campaign->recipients_count > 10000) {
            $this->onQueue('bulk-email');
        } elseif ($campaign->recipients_count > 1000) {
            $this->onQueue('medium-email');
        } else {
            $this->onQueue('default');
        }
    }

    public function handle(): void
    {
        try {
            // Проверяем актуальность кампании
            $this->campaign->refresh();
            
            if (!in_array($this->campaign->status, ['active', 'paused'])) {
                Log::info("Campaign {$this->campaign->id} is not active, skipping batch {$this->batchNumber}");
                return;
            }
            
            // Если кампания приостановлена, переносим выполнение
            if ($this->campaign->status === 'paused') {
                $this->release(300); // Повторить через 5 минут
                return;
            }
            
            Log::info("Processing batch {$this->batchNumber} for campaign {$this->campaign->id}");
            
            // Получаем пакет неотправленных emails
            $batchSize = $this->campaign->batch_size;
            $offset = ($this->batchNumber - 1) * $batchSize;
            
            $pendingEmails = EmailLog::where('campaign_id', $this->campaign->id)
                ->where('status', 'pending')
                ->offset($offset)
                ->limit($batchSize)
                ->get();
            
            if ($pendingEmails->isEmpty()) {
                Log::info("No pending emails found for batch {$this->batchNumber} of campaign {$this->campaign->id}");
                $this->checkCampaignCompletion();
                return;
            }
            
            $sentCount = 0;
            $failedCount = 0;
            $delayBetweenEmails = 60 / $this->campaign->emails_per_minute; // Секунд между письмами
            
            foreach ($pendingEmails as $emailLog) {
                // Проверяем статус кампании перед каждой отправкой
                $this->campaign->refresh();
                if ($this->campaign->status !== 'active') {
                    Log::info("Campaign {$this->campaign->id} paused during batch processing");
                    break;
                }
                
                try {
                    Mail::to($emailLog->email)->send(new CampaignEmail($this->campaign));
                    
                    $emailLog->update([
                        'status' => 'sent',
                        'sent_at' => now()
                    ]);
                    
                    $sentCount++;
                    
                    // Задержка между отправками для соблюдения лимитов
                    if ($delayBetweenEmails > 0) {
                        usleep($delayBetweenEmails * 1000000); // Конвертируем в микросекунды
                    }
                    
                } catch (\Exception $e) {
                    $emailLog->update([
                        'status' => 'failed',
                        'error_message' => $e->getMessage()
                    ]);
                    
                    $failedCount++;
                    Log::error("Failed to send email to {$emailLog->email}: " . $e->getMessage(), [
                        'campaign_id' => $this->campaign->id,
                        'batch' => $this->batchNumber
                    ]);
                }
            }
            
            // Обновляем статистику кампании
            $this->campaign->increment('sent_count', $sentCount);
            $this->campaign->increment('failed_count', $failedCount);
            $this->campaign->update(['current_batch' => $this->batchNumber]);
            
            Log::info("Batch {$this->batchNumber} completed for campaign {$this->campaign->id}. Sent: {$sentCount}, Failed: {$failedCount}");
            
            // Планируем следующий пакет
            $this->scheduleNextBatch();
            
        } catch (\Exception $e) {
            Log::error("Batch processing failed for campaign {$this->campaign->id}, batch {$this->batchNumber}: " . $e->getMessage());
            throw $e;
        }
    }
    
    protected function scheduleNextBatch(): void
    {
        $remainingEmails = EmailLog::where('campaign_id', $this->campaign->id)
            ->where('status', 'pending')
            ->count();
            
        if ($remainingEmails > 0 && $this->campaign->status === 'active') {
            $nextBatchTime = now()->addSeconds($this->campaign->delay_between_batches);
            
            // Обновляем время следующего пакета
            $this->campaign->update(['next_batch_at' => $nextBatchTime]);
            
            // Планируем следующую задачу
            ProcessEmailBatch::dispatch($this->campaign, $this->batchNumber + 1)
                ->delay($nextBatchTime);
                
            Log::info("Next batch scheduled for campaign {$this->campaign->id} at {$nextBatchTime}");
        } else {
            $this->checkCampaignCompletion();
        }
    }
    
    protected function checkCampaignCompletion(): void
    {
        $remainingEmails = EmailLog::where('campaign_id', $this->campaign->id)
            ->where('status', 'pending')
            ->count();
            
        if ($remainingEmails === 0) {
            $this->campaign->update([
                'status' => 'completed',
                'next_batch_at' => null
            ]);
            
            Log::info("Campaign {$this->campaign->id} completed successfully");
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Batch job permanently failed for campaign {$this->campaign->id}, batch {$this->batchNumber}: " . $exception->getMessage());
        
        // Не помечаем всю кампанию как failed, только текущий пакет
        // Следующий пакет может быть обработан успешно
    }
}
