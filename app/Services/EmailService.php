<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Jobs\SendEmailCampaign;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Exception;

class EmailService
{
    protected int $maxAttempts = 3;
    protected array $retryableErrors = [
        'Connection timed out',
        'Connection refused',
        'Temporary failure'
    ];

    public function startCampaign(EmailCampaign $campaign): void
    {
        // Запускаем отправку через Job
        SendEmailCampaign::dispatch($campaign);
    }

    public function processCampaign(EmailCampaign $campaign): void
    {
        $recipients = $campaign->pendingRecipients()->get();
        
        foreach ($recipients as $recipient) {
            if ($campaign->fresh()->status !== 'sending') {
                break; // Остановка если кампания приостановлена
            }

            try {
                $this->sendEmailToRecipient($campaign, $recipient);
                
                $campaign->increment('sent_count');
                
                // Задержка между отправками для избежания спама
                if ($campaign->delay_seconds > 0) {
                    sleep($campaign->delay_seconds);
                }
                
            } catch (Exception $e) {
                Log::error('Email sending failed', [
                    'campaign_id' => $campaign->id,
                    'recipient_id' => $recipient->id,
                    'error' => $e->getMessage()
                ]);
                
                $this->handleFailedEmail($campaign, $recipient, $e->getMessage());
            }
        }

        // Проверяем завершение кампании
        $this->checkCampaignCompletion($campaign);
    }

    protected function sendEmailToRecipient(EmailCampaign $campaign, EmailRecipient $recipient): void
    {
        $emailContent = $this->renderEmail($campaign, [
            'name' => $recipient->name ?: 'Уважаемый клиент',
            'email' => $recipient->email
        ]);

        Mail::send([], [], function ($message) use ($campaign, $recipient, $emailContent) {
            $settings = $campaign->settings ?? [];
            
            $message->to($recipient->email, $recipient->name)
                   ->subject($campaign->subject)
                   ->from($settings['from_email'] ?? 'email@weebs.ru', 
                          $settings['from_name'] ?? 'Конструктор Сайтов')
                   ->html($emailContent);
                   
            // Устанавливаем заголовки для избежания спама
            $message->getHeaders()->addTextHeader('X-Mailer', 'Laravel');
            $message->getHeaders()->addTextHeader('X-Priority', '3');
            $message->getHeaders()->addTextHeader('X-Campaign-ID', $campaign->id);
        });

        $recipient->markAsSent();
    }

    public function renderEmail(EmailCampaign $campaign, array $variables = []): string
    {
        $template = $this->getEmailTemplate($campaign->template);
        $content = $campaign->content;

        // Замена переменных
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }

        // Вставляем содержимое в шаблон
        $template = str_replace('{{content}}', $content, $template);

        return $template;
    }

    protected function getEmailTemplate(string $templateName): string
    {
        $templates = [
            'sales' => $this->getSalesTemplate(),
            'informational' => $this->getInformationalTemplate(),
            'promotional' => $this->getPromotionalTemplate()
        ];

        return $templates[$templateName] ?? $templates['sales'];
    }

    protected function getSalesTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .header { text-align: center; padding: 20px 0; border-bottom: 2px solid #007bff; margin-bottom: 20px; }
        .header h1 { color: #007bff; margin: 0; }
        .content { padding: 20px 0; }
        .highlight { background-color: #fff3cd; padding: 15px; border-left: 4px solid #ffc107; margin: 20px 0; }
        .cta-button { display: inline-block; padding: 12px 30px; background-color: #28a745; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; font-weight: bold; }
        .footer { text-align: center; padding: 20px 0; border-top: 1px solid #eee; color: #666; font-size: 12px; }
        .problems { background-color: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .solution { background-color: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🚀 Конструктор Сайтов</h1>
            <p>Создайте профессиональный сайт за считанные минуты!</p>
        </div>
        
        <div class="content">
            <h2>Привет, {{name}}!</h2>
            
            <div class="problems">
                <h3>❌ Знакомы ли вам эти проблемы?</h3>
                <ul>
                    <li>Нужен сайт, но нет времени и денег на разработчика?</li>
                    <li>Хотите быстро запустить свой бизнес в интернет?</li>
                    <li>Устали от сложных конструкторов с кучей лишних функций?</li>
                    <li>Нужен современный и адаптивный дизайн?</li>
                </ul>
            </div>
            
            <div class="solution">
                <h3>✅ У нас есть решение!</h3>
                <p><strong>Наш конструктор сайтов</strong> - это:</p>
                <ul>
                    <li>🎨 <strong>Красивые готовые блоки</strong> - просто выбираете и настраиваете</li>
                    <li>⚡ <strong>Быстрое создание</strong> - сайт готов за 15 минут</li>
                    <li>📱 <strong>Адаптивный дизайн</strong> - отлично выглядит на всех устройствах</li>
                    <li>💰 <strong>Доступная цена</strong> - в разы дешевле услуг веб-студии</li>
                    <li>🔧 <strong>Простота использования</strong> - без технических знаний</li>
                </ul>
            </div>
            
            {{content}}
            
            <div class="highlight">
                <h3>🎁 Специальное предложение только сегодня!</h3>
                <p>Получите <strong>50% скидку</strong> на создание вашего первого сайта!</p>
                <p><strong>Вместо 5000₽ - всего 2500₽</strong></p>
            </div>
            
            <div style="text-align: center;">
                <a href="https://ваш-сайт.ru/constructor" class="cta-button">
                    🚀 Создать сайт прямо сейчас!
                </a>
            </div>
            
            <p style="color: #666; font-size: 14px;">
                P.S. Предложение ограничено - осталось всего 24 часа!<br>
                Не упустите шанс получить профессиональный сайт по специальной цене.
            </p>
        </div>
        
        <div class="footer">
            <p>С уважением, команда Конструктора Сайтов</p>
            <p>Если письмо отображается некорректно, <a href="#">посмотрите его в браузере</a></p>
            <p>Чтобы отписаться от рассылки, <a href="#">нажмите здесь</a></p>
        </div>
    </div>
</body>
</html>';
    }

    protected function getInformationalTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Georgia, serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f9f9f9; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; padding: 30px; border-radius: 8px; }
        .header { text-align: center; padding-bottom: 20px; border-bottom: 1px solid #eee; }
        .content { padding: 20px 0; }
        .footer { text-align: center; padding: 15px 0; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #2c3e50;">Информационная рассылка</h1>
        </div>
        
        <div class="content">
            <p>Здравствуйте, {{name}}!</p>
            {{content}}
        </div>
        
        <div class="footer">
            <p>С уважением, команда проекта</p>
        </div>
    </div>
</body>
</html>';
    }

    protected function getPromotionalTemplate(): string
    {
        return '
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{subject}}</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .container { max-width: 600px; margin: 20px auto; background-color: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-align: center; padding: 30px; }
        .content { padding: 30px; }
        .promo-block { background: linear-gradient(45deg, #ffd700, #ffed4e); padding: 20px; border-radius: 10px; text-align: center; margin: 20px 0; }
        .footer { background-color: #f8f9fa; text-align: center; padding: 20px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Специальное предложение!</h1>
        </div>
        
        <div class="content">
            <p>Привет, {{name}}!</p>
            
            <div class="promo-block">
                <h2 style="margin: 0; color: #333;">АКЦИЯ ОГРАНИЧЕННОГО ВРЕМЕНИ!</h2>
            </div>
            
            {{content}}
        </div>
        
        <div class="footer">
            <p>Не упустите возможность!</p>
        </div>
    </div>
</body>
</html>';
    }

    protected function handleFailedEmail(EmailCampaign $campaign, EmailRecipient $recipient, string $error): void
    {
        $shouldRetry = $recipient->attempts < $this->maxAttempts && 
                      $this->isRetryableError($error);

        if ($shouldRetry) {
            // Планируем повторную отправку
            $recipient->increment('attempts');
        } else {
            $recipient->markAsFailed($error);
            $campaign->increment('failed_count');
        }
    }

    protected function isRetryableError(string $error): bool
    {
        foreach ($this->retryableErrors as $retryableError) {
            if (stripos($error, $retryableError) !== false) {
                return true;
            }
        }
        
        return false;
    }

    protected function checkCampaignCompletion(EmailCampaign $campaign): void
    {
        $campaign = $campaign->fresh();
        $totalProcessed = $campaign->sent_count + $campaign->failed_count;
        
        if ($totalProcessed >= $campaign->total_recipients) {
            $campaign->update([
                'status' => 'completed',
                'completed_at' => now()
            ]);
        }
    }
}
