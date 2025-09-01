<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\EmailLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Mail\CampaignEmail;
use Illuminate\Support\Facades\Queue;
use App\Jobs\SendEmailCampaign;

class EmailCampaignController extends Controller
{
    public function index()
    {
        $campaigns = EmailCampaign::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('email.campaigns.index', compact('campaigns'));
    }

    public function create()
    {
        $templates = EmailTemplate::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();
            
        return view('email.campaigns.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:email_templates,id',
            'scheduled_at' => 'nullable|date|after:now',
            'email_list' => 'required|file|mimes:txt,csv',
            'batch_size' => 'nullable|integer|min:1|max:1000',
            'delay_between_batches' => 'nullable|integer|min:30|max:3600',
            'emails_per_minute' => 'nullable|integer|min:1|max:60'
        ]);

        // Загрузка списка email адресов
        $emailFile = $request->file('email_list');
        $emails = $this->parseEmailFile($emailFile);
        
        $emailCount = count($emails);
        
        // Автоматическая настройка параметров в зависимости от размера базы
        $batchSettings = $this->calculateOptimalBatchSettings($emailCount, $validated);

        $campaign = EmailCampaign::create([
            'name' => $validated['name'],
            'subject' => $validated['subject'],
            'content' => $validated['content'],
            'template' => $validated['template_id'] ?? null,
            'scheduled_at' => $validated['scheduled_at'] ?? null,
            'recipients_count' => $emailCount,
            'batch_size' => $batchSettings['batch_size'],
            'delay_between_batches' => $batchSettings['delay_between_batches'],
            'emails_per_minute' => $batchSettings['emails_per_minute'],
            'user_id' => Auth::id()
        ]);

        // Создание логов для каждого email пакетами для больших списков
        $this->createEmailLogs($campaign, $emails);

        if ($validated['scheduled_at']) {
            // Запланированная рассылка
            $campaign->update(['status' => 'scheduled']);
        } else {
            // Немедленная рассылка
            $this->startCampaign($campaign);
        }

        return redirect()->route('email-campaigns.index')
            ->with('success', "Кампания создана успешно! Настроена для отправки {$emailCount} писем пакетами по {$batchSettings['batch_size']} с интервалом {$batchSettings['delay_between_batches']} сек.");
    }

    /**
     * Рассчитывает оптимальные настройки пакетной отправки
     */
    private function calculateOptimalBatchSettings(int $emailCount, array $validated): array
    {
        // Базовые настройки в зависимости от размера базы
        if ($emailCount > 100000) {
            // Очень большая база (100k+)
            $defaults = [
                'batch_size' => 50,
                'delay_between_batches' => 300, // 5 минут
                'emails_per_minute' => 5
            ];
        } elseif ($emailCount > 50000) {
            // Большая база (50k+)
            $defaults = [
                'batch_size' => 100,
                'delay_between_batches' => 180, // 3 минуты
                'emails_per_minute' => 8
            ];
        } elseif ($emailCount > 10000) {
            // Средняя база (10k+)
            $defaults = [
                'batch_size' => 200,
                'delay_between_batches' => 120, // 2 минуты
                'emails_per_minute' => 10
            ];
        } elseif ($emailCount > 1000) {
            // Малая база (1k+)
            $defaults = [
                'batch_size' => 300,
                'delay_between_batches' => 60, // 1 минута
                'emails_per_minute' => 15
            ];
        } else {
            // Очень малая база
            $defaults = [
                'batch_size' => 500,
                'delay_between_batches' => 30, // 30 секунд
                'emails_per_minute' => 20
            ];
        }
        
        // Переопределяем настройки пользователя, если они заданы
        return [
            'batch_size' => $validated['batch_size'] ?? $defaults['batch_size'],
            'delay_between_batches' => $validated['delay_between_batches'] ?? $defaults['delay_between_batches'],
            'emails_per_minute' => $validated['emails_per_minute'] ?? $defaults['emails_per_minute']
        ];
    }
    
    /**
     * Создает записи email логов пакетами для оптимизации
     */
    private function createEmailLogs(EmailCampaign $campaign, array $emails): void
    {
        $emailLogs = [];
        $batchSize = 1000; // Пакеты для вставки в БД
        
        foreach ($emails as $index => $email) {
            $emailLogs[] = [
                'campaign_id' => $campaign->id,
                'email' => trim($email),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Вставляем пакетами для оптимизации
            if (count($emailLogs) >= $batchSize || $index === count($emails) - 1) {
                EmailLog::insert($emailLogs);
                $emailLogs = [];
            }
        }
    }

    public function show(EmailCampaign $emailCampaign)
    {
        $this->authorize('view', $emailCampaign);
        
        $stats = [
            'total' => $emailCampaign->recipients_count,
            'sent' => $emailCampaign->sent_count,
            'failed' => $emailCampaign->failed_count,
            'pending' => $emailCampaign->recipients_count - $emailCampaign->sent_count - $emailCampaign->failed_count,
            'success_rate' => $emailCampaign->success_rate
        ];
        
        $logs = $emailCampaign->logs()->orderBy('created_at', 'desc')->paginate(20);
        
        return view('email.campaigns.show', compact('emailCampaign', 'stats', 'logs'));
    }

    public function edit(EmailCampaign $emailCampaign)
    {
        $this->authorize('update', $emailCampaign);
        
        if ($emailCampaign->status !== 'draft') {
            return redirect()->back()->with('error', 'Можно редактировать только черновики');
        }
        
        $templates = EmailTemplate::where('user_id', Auth::id())
            ->where('is_active', true)
            ->get();
            
        return view('email.campaigns.edit', compact('emailCampaign', 'templates'));
    }

    public function update(Request $request, EmailCampaign $emailCampaign)
    {
        $this->authorize('update', $emailCampaign);
        
        if ($emailCampaign->status !== 'draft') {
            return redirect()->back()->with('error', 'Можно редактировать только черновики');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'template_id' => 'nullable|exists:email_templates,id',
            'scheduled_at' => 'nullable|date|after:now'
        ]);

        $emailCampaign->update($validated);

        return redirect()->route('email-campaigns.index')
            ->with('success', 'Кампания обновлена успешно!');
    }

    public function destroy(EmailCampaign $emailCampaign)
    {
        $this->authorize('delete', $emailCampaign);
        
        if ($emailCampaign->status === 'active') {
            return redirect()->back()->with('error', 'Нельзя удалить активную кампанию');
        }
        
        $emailCampaign->delete();
        
        return redirect()->route('email-campaigns.index')
            ->with('success', 'Кампания удалена успешно!');
    }

    public function start(EmailCampaign $emailCampaign)
    {
        $this->authorize('update', $emailCampaign);
        
        if ($emailCampaign->status !== 'draft' && $emailCampaign->status !== 'paused') {
            return redirect()->back()->with('error', 'Можно запускать только черновики или приостановленные кампании');
        }
        
        $this->startCampaign($emailCampaign);
        
        return redirect()->back()->with('success', 'Рассылка запущена!');
    }

    public function pause(EmailCampaign $emailCampaign)
    {
        $this->authorize('update', $emailCampaign);
        
        if ($emailCampaign->status !== 'active') {
            return redirect()->back()->with('error', 'Можно приостановить только активные кампании');
        }
        
        $emailCampaign->update(['status' => 'paused']);
        
        return redirect()->back()->with('success', 'Рассылка приостановлена!');
    }

    private function parseEmailFile($file)
    {
        $content = file_get_contents($file->getPathname());
        $emails = [];
        
        // Определяем тип файла и парсим соответственно
        if ($file->getClientOriginalExtension() === 'csv') {
            $lines = str_getcsv($content, "\n");
            foreach ($lines as $line) {
                $data = str_getcsv($line);
                if (filter_var($data[0], FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $data[0];
                }
            }
        } else {
            // Для txt файлов
            $lines = explode("\n", $content);
            foreach ($lines as $line) {
                $email = trim($line);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $emails[] = $email;
                }
            }
        }
        
        return array_unique($emails);
    }

    private function startCampaign(EmailCampaign $campaign)
    {
        $campaign->update([
            'status' => 'active',
            'sent_at' => now()
        ]);
        
        // Запуск задания в очереди
        SendEmailCampaign::dispatch($campaign);
    }
}
