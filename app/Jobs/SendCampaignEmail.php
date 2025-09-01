<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Mail\CampaignMail;
use App\Models\EmailCampaign;
use App\Models\EmailSubscriber;
use App\Models\EmailLog;
use App\Services\EmailDeliveryService;

class SendCampaignEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $campaignId;
    public array $subscriberIds;
    public int $tries = 3;
    public int $timeout = 300;
    public int $retryAfter = 60;

    public function __construct(int $campaignId, array $subscriberIds)
    {
        $this->campaignId = $campaignId;
        $this->subscriberIds = $subscriberIds;
    }

    public function handle(EmailDeliveryService $deliveryService): void
    {
        $campaign = EmailCampaign::findOrFail($this->campaignId);
        
        // Обновляем статус кампании
        if ($campaign->status === EmailCampaign::STATUS_SCHEDULED) {
            $campaign->update([
                'status' => EmailCampaign::STATUS_SENDING,
                'started_at' => now()
            ]);
        }

        $subscribers = EmailSubscriber::whereIn('id', $this->subscriberIds)
                                     ->where('status', EmailSubscriber::STATUS_ACTIVE)
                                     ->get();

        foreach ($subscribers as $subscriber) {
            try {
                $this->sendToSubscriber($campaign, $subscriber, $deliveryService);
                
                // Throttling между отправками
                usleep(100000); // 0.1 секунды
                
            } catch (\Throwable $e) {
                Log::error('Campaign email send failed', [
                    'campaign_id' => $this->campaignId,
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);

                // Создаем лог неудачной отправки
                EmailLog::create([
                    'campaign_id' => $this->campaignId,
                    'subscriber_id' => $subscriber->id,
                    'email' => $subscriber->email,
                    'subject' => $campaign->subject,
                    'status' => EmailLog::STATUS_FAILED,
                    'failed_at' => now(),
                    'error_message' => $e->getMessage(),
                    'retry_count' => 0,
                    'tracking_token' => $this->generateTrackingToken()
                ]);

                // Обновляем счетчики кампании
                $campaign->increment('failed_count');
            }
        }

        // Проверяем, завершена ли кампания
        $this->checkCampaignCompletion($campaign);
    }

    private function sendToSubscriber(EmailCampaign $campaign, EmailSubscriber $subscriber, EmailDeliveryService $deliveryService): void
    {
        $trackingToken = $this->generateTrackingToken();
        
        // Создаем лог отправки
        $emailLog = EmailLog::create([
            'campaign_id' => $this->campaignId,
            'subscriber_id' => $subscriber->id,
            'email' => $subscriber->email,
            'subject' => $campaign->subject,
            'status' => EmailLog::STATUS_PENDING,
            'tracking_token' => $trackingToken,
            'metadata' => [
                'user_agent' => request()->header('User-Agent'),
                'ip_address' => request()->ip()
            ]
        ]);

        // Проверяем доступность SMTP
        if (!$deliveryService->isAvailable()) {
            throw new \Exception('SMTP server is not available');
        }

        // Отправляем письмо
        Mail::to($subscriber->email)->send(
            new CampaignMail($campaign, $subscriber, $trackingToken)
        );

        // Обновляем лог успешной отправки
        $emailLog->markAsSent();
        
        // Обновляем счетчики кампании
        $campaign->increment('sent_count');
    }

    private function generateTrackingToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    private function checkCampaignCompletion(EmailCampaign $campaign): void
    {
        $totalProcessed = $campaign->sent_count + $campaign->failed_count;
        
        if ($totalProcessed >= $campaign->total_recipients) {
            $campaign->update([
                'status' => EmailCampaign::STATUS_COMPLETED,
                'completed_at' => now()
            ]);

            Log::info('Campaign completed', [
                'campaign_id' => $campaign->id,
                'total_recipients' => $campaign->total_recipients,
                'sent_count' => $campaign->sent_count,
                'failed_count' => $campaign->failed_count,
                'success_rate' => $campaign->success_rate
            ]);
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('SendCampaignEmail job failed completely', [
            'campaign_id' => $this->campaignId,
            'subscriber_ids' => $this->subscriberIds,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        // Помечаем все письма как неуспешные
        EmailLog::where('campaign_id', $this->campaignId)
                ->whereIn('subscriber_id', $this->subscriberIds)
                ->where('status', EmailLog::STATUS_PENDING)
                ->update([
                    'status' => EmailLog::STATUS_FAILED,
                    'failed_at' => now(),
                    'error_message' => 'Job failed: ' . $exception->getMessage()
                ]);
    }
}
