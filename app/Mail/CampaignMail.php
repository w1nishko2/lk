<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\EmailCampaign;
use App\Models\EmailSubscriber;
use App\Models\EmailTemplate;

class CampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    public EmailCampaign $campaign;
    public EmailSubscriber $subscriber;
    public string $trackingToken;
    public ?EmailTemplate $template;

    public function __construct(EmailCampaign $campaign, EmailSubscriber $subscriber, string $trackingToken)
    {
        $this->campaign = $campaign;
        $this->subscriber = $subscriber;
        $this->trackingToken = $trackingToken;
        $this->template = $campaign->template_id ? EmailTemplate::find($campaign->template_id) : null;
    }

    public function build()
    {
        $unsubscribeUrl = url('/unsubscribe', [
            'token' => $this->trackingToken,
            'email' => $this->subscriber->email
        ]);

        $trackingUrl = url('/track/open', ['token' => $this->trackingToken]);
        
        // Переменные для шаблона
        $variables = [
            'subscriber_name' => $this->subscriber->full_name ?: 'Подписчик',
            'subscriber_email' => $this->subscriber->email,
            'unsubscribe_url' => $unsubscribeUrl,
            'tracking_pixel' => '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none;" />',
            'campaign_name' => $this->campaign->name,
            'preview_text' => $this->campaign->preview_text
        ];

        // Определяем тему письма
        $subject = $this->template 
            ? $this->template->renderSubject($variables)
            : $this->campaign->subject;

        // Определяем содержимое
        $htmlContent = $this->template 
            ? $this->template->renderHtml($variables)
            : $this->getDefaultHtmlContent($variables);

        $textContent = $this->template 
            ? $this->template->renderText($variables) 
            : $this->getDefaultTextContent($variables);

        return $this->subject($subject)
            ->from($this->campaign->sender_email ?: config('mail.from.address'), 
                   $this->campaign->sender_name ?: config('mail.from.name'))
            ->html($htmlContent)
            ->text($textContent)
            ->withSymfonyMessage(function ($message) use ($unsubscribeUrl) {
                // Заголовки для улучшения доставляемости
                $headers = $message->getHeaders();
                $headers->addTextHeader('List-Unsubscribe', '<' . $unsubscribeUrl . '>');
                $headers->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
                $headers->addTextHeader('X-Campaign-ID', $this->campaign->id);
                $headers->addTextHeader('X-Subscriber-ID', $this->subscriber->id);
            });
    }

    private function getDefaultHtmlContent(array $variables): string
    {
        return view('emails.campaign-default', [
            'campaign' => $this->campaign,
            'subscriber' => $this->subscriber,
            'variables' => $variables
        ])->render();
    }

    private function getDefaultTextContent(array $variables): string
    {
        return view('emails.campaign-default-text', [
            'campaign' => $this->campaign, 
            'subscriber' => $this->subscriber,
            'variables' => $variables
        ])->render();
    }
}
