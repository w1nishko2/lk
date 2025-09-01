<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignMailLegacy extends Mailable
{
    use Queueable, SerializesModels;

    public string $emailSubject;
    public string $previewText;
    public string $recipientEmail;
    public string $messageContent;

    public function __construct(string $subject, string $previewText, string $recipientEmail)
    {
        $this->emailSubject = $subject;
        $this->previewText = $previewText;
        $this->recipientEmail = $recipientEmail;
        $this->messageContent = $this->generateContent();
    }

    private function generateContent(): string
    {
        return "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #f8f9fa; padding: 20px; text-align: center;'>
                <h1 style='color: #333; margin: 0;'>Создайте сайт, который продаёт</h1>
            </div>
            
            <div style='padding: 30px 20px; background: white;'>
                <h2 style='color: #2c5282; margin-bottom: 20px;'>Привет!</h2>
                
                <p style='font-size: 16px; line-height: 1.6; color: #444;'>
                    Хотите, чтобы ваш сайт не просто красиво выглядел, а реально приносил клиентов и прибыль?
                </p>
                
                <p style='font-size: 16px; line-height: 1.6; color: #444;'>
                    Мы поможем вам создать сайт, который:
                </p>
                
                <ul style='font-size: 16px; line-height: 1.8; color: #444; padding-left: 20px;'>
                    <li>Привлекает целевую аудиторию</li>
                    <li>Конвертирует посетителей в клиентов</li>
                    <li>Работает как надежный источник заказов 24/7</li>
                </ul>
                
                <div style='text-align: center; margin: 30px 0;'>
                    <a href='https://lk.weebs.ru' style='background: #3182ce; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-weight: bold;'>
                        Получить консультацию
                    </a>
                </div>
                
                <p style='font-size: 14px; color: #666; margin-top: 30px;'>
                    С уважением,<br>
                    Команда разработчиков
                </p>
            </div>
            
            <div style='background: #f8f9fa; padding: 15px 20px; font-size: 12px; color: #666; text-align: center;'>
                <p>Если вы не хотите получать такие письма, <a href='#'>отписаться</a></p>
            </div>
        </div>";
    }

    public function build()
    {
        return $this->subject($this->emailSubject)
                    ->html($this->messageContent)
                    ->text($this->generateTextVersion());
    }

    private function generateTextVersion(): string
    {
        return "
Создайте сайт, который продаёт

Привет!

Хотите, чтобы ваш сайт не просто красиво выглядел, а реально приносил клиентов и прибыль?

Мы поможем вам создать сайт, который:
- Привлекает целевую аудиторию
- Конвертирует посетителей в клиентов  
- Работает как надежный источник заказов 24/7

Получить консультацию: https://lk.weebs.ru

С уважением,
Команда разработчиков

Если вы не хотите получать такие письма, отписаться можно по ссылке выше.
        ";
    }
}
