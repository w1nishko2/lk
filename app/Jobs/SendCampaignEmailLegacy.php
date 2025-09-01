<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\CampaignMailLegacy;

class SendCampaignEmailLegacy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public array $emails;
    public string $subject;
    public string $previewText;
    public int $tries = 3;
    public int $timeout = 300;
    public int $retryAfter = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(array $emails, string $subject, string $previewText = '')
    {
        $this->emails = $emails;
        $this->subject = $subject;
        $this->previewText = $previewText;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->emails as $email) {
            try {
                Mail::to($email)->send(new CampaignMailLegacy($this->subject, $this->previewText, $email));
                
                Log::info('Campaign email sent successfully', [
                    'email' => $email,
                    'subject' => $this->subject
                ]);
                
                // Добавляем небольшую паузу между отправками
                usleep(100000); // 0.1 секунды
                
            } catch (\Exception $e) {
                Log::error('Campaign send failed', [
                    'email' => $email,
                    'error' => $e->getMessage(),
                    'exception' => get_class($e)
                ]);
                
                // Проверяем тип ошибки и даем рекомендации
                if (str_contains($e->getMessage(), 'Connection refused') || 
                    str_contains($e->getMessage(), 'Connection could not be established')) {
                    Log::info('Mail transport issue detected. Suggestion: проверьте MAIL_HOST и MAIL_PORT в .env и перезапустите воркер (php artisan config:clear && php artisan queue:restart).');
                } elseif (str_contains($e->getMessage(), 'Authentication failed')) {
                    Log::info('Mail auth issue detected. Suggestion: проверьте MAIL_USERNAME и MAIL_PASSWORD в .env.');
                }
                
                // Не прерываем обработку других email'ов, продолжаем
                continue;
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SendCampaignEmailLegacy job failed completely', [
            'exception' => $exception->getMessage(),
            'emails_count' => count($this->emails),
            'subject' => $this->subject
        ]);
    }
}
