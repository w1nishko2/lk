<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\EmailCampaign;
use App\Models\EmailRecipient;
use App\Services\EmailService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class EmailCampaignController extends Controller
{
    protected EmailService $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    public function index(): View
    {
        $campaigns = EmailCampaign::orderBy('created_at', 'desc')->paginate(10);
        
        return view('email-campaigns.index', compact('campaigns'));
    }

    public function create(): View
    {
        $templates = $this->getEmailTemplates();
        
        return view('email-campaigns.create', compact('templates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template' => 'required|string',
            'delay_seconds' => 'required|integer|min:10|max:3600',
            'recipients_file' => 'required|file|mimes:txt|max:10240'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $campaign = EmailCampaign::create([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'template' => $request->template,
            'delay_seconds' => $request->delay_seconds,
            'settings' => [
                'from_name' => $request->from_name ?? 'Конструктор Сайтов',
                'from_email' => 'email@weebs.ru'
            ]
        ]);

        // Обработка файла с получателями
        $this->processRecipientsFile($campaign, $request->file('recipients_file'));

        return redirect()->route('email-campaigns.show', $campaign)
                        ->with('success', 'Кампания создана успешно');
    }

    public function show(EmailCampaign $emailCampaign): View
    {
        $emailCampaign->load(['recipients' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }]);

        $stats = [
            'total' => $emailCampaign->total_recipients,
            'sent' => $emailCampaign->sent_count,
            'failed' => $emailCampaign->failed_count,
            'pending' => $emailCampaign->total_recipients - $emailCampaign->sent_count - $emailCampaign->failed_count
        ];

        return view('email-campaigns.show', compact('emailCampaign', 'stats'));
    }

    public function start(EmailCampaign $emailCampaign): JsonResponse
    {
        if (!$emailCampaign->canStart()) {
            return response()->json([
                'success' => false,
                'message' => 'Кампания не может быть запущена'
            ]);
        }

        $emailCampaign->update([
            'status' => 'sending',
            'started_at' => now()
        ]);

        // Запуск в фоне
        $this->emailService->startCampaign($emailCampaign);

        return response()->json([
            'success' => true,
            'message' => 'Рассылка запущена'
        ]);
    }

    public function pause(EmailCampaign $emailCampaign): JsonResponse
    {
        $emailCampaign->update(['status' => 'paused']);

        return response()->json([
            'success' => true,
            'message' => 'Рассылка приостановлена'
        ]);
    }

    public function preview(EmailCampaign $emailCampaign): View
    {
        $content = $this->emailService->renderEmail($emailCampaign, [
            'name' => 'Имя Получателя',
            'email' => 'example@example.com'
        ]);

        return view('email-campaigns.preview', compact('emailCampaign', 'content'));
    }

    private function processRecipientsFile(EmailCampaign $campaign, $file): void
    {
        $content = file_get_contents($file->getRealPath());
        $emails = array_filter(array_map('trim', explode("\n", $content)));
        
        $validEmails = [];
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = [
                    'campaign_id' => $campaign->id,
                    'email' => $email,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
        }

        if (!empty($validEmails)) {
            // Разбиваем на чанки для избежания превышения лимитов памяти
            $chunks = array_chunk($validEmails, 1000);
            foreach ($chunks as $chunk) {
                EmailRecipient::insert($chunk);
            }
            
            $campaign->update(['total_recipients' => count($validEmails)]);
        }
    }

    private function getEmailTemplates(): array
    {
        return [
            'sales' => 'Продающий шаблон',
            'informational' => 'Информационный шаблон',
            'promotional' => 'Рекламный шаблон'
        ];
    }
}
