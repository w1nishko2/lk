<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailQueue;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MassEmailMail;

class MassEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-mass 
                            {--import : Import emails from file to database}
                            {--send : Send emails from queue}
                            {--batch=5 : Number of emails to send per batch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mass email sending system. Use --import to load emails, --send to process queue';

    private $emailFilePath;
    
    public function __construct()
    {
        parent::__construct();
        $this->emailFilePath = base_path('baseemail/test.txt');
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('import')) {
            $this->importEmails();
        } elseif ($this->option('send')) {
            $this->sendEmails();
        } else {
            $this->info('Please specify --import or --send option');
            $this->info('Examples:');
            $this->info('  php artisan email:send-mass --import');
            $this->info('  php artisan email:send-mass --send --batch=5');
        }
    }
    
    /**
     * Import emails from file to database
     */
    private function importEmails()
    {
        if (!file_exists($this->emailFilePath)) {
            $this->error("Email file not found: {$this->emailFilePath}");
            return;
        }
        
        $this->info('Starting email import...');
        
        $handle = fopen($this->emailFilePath, 'r');
        if (!$handle) {
            $this->error('Could not open email file');
            return;
        }
        
        $imported = 0;
        $skipped = 0;
        $batch = [];
        $batchSize = 1000;
        
        while (($line = fgets($handle)) !== false) {
            $email = trim($line);
            
            // Валидация email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $skipped++;
                continue;
            }
            
            $batch[] = [
                'email' => $email,
                'status' => 'pending',
                'attempts' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            if (count($batch) >= $batchSize) {
                $this->insertBatch($batch);
                $imported += count($batch);
                $batch = [];
                
                $this->info("Imported: {$imported}, Skipped: {$skipped}");
            }
        }
        
        // Insert remaining emails
        if (!empty($batch)) {
            $this->insertBatch($batch);
            $imported += count($batch);
        }
        
        fclose($handle);
        
        $this->info("Import completed! Imported: {$imported}, Skipped: {$skipped}");
    }
    
    /**
     * Insert batch of emails with duplicate handling
     */
    private function insertBatch($batch)
    {
        try {
            EmailQueue::insertOrIgnore($batch);
        } catch (\Exception $e) {
            Log::error('Batch insert failed: ' . $e->getMessage());
        }
    }
    
    /**
     * Send emails from queue
     */
    private function sendEmails()
    {
        $batchSize = (int) $this->option('batch');
        
        $this->info("Processing {$batchSize} emails...");
        
        $emails = EmailQueue::pending()
            ->where('attempts', '<', 3) // Максимум 3 попытки
            ->limit($batchSize)
            ->get();
            
        if ($emails->isEmpty()) {
            $this->info('No emails to send');
            return;
        }
        
        $sent = 0;
        $failed = 0;
        
        foreach ($emails as $emailQueue) {
            try {
                $this->sendSingleEmail($emailQueue->email);
                $emailQueue->markAsSent();
                $sent++;
                $this->info("✓ Sent to: {$emailQueue->email}");
            } catch (\Exception $e) {
                $emailQueue->markAsFailed($e->getMessage());
                $failed++;
                $this->error("✗ Failed to send to: {$emailQueue->email} - {$e->getMessage()}");
                Log::error('Email send failed', [
                    'email' => $emailQueue->email,
                    'error' => $e->getMessage()
                ]);
            }
            
            // Пауза между отправками для избежания блокировки
            usleep(500000); // 0.5 секунды
        }
        
        $this->info("Batch completed! Sent: {$sent}, Failed: {$failed}");
        
        // Статистика
        $total = EmailQueue::count();
        $pending = EmailQueue::pending()->count();
        $sentTotal = EmailQueue::sent()->count();
        $failedTotal = EmailQueue::failed()->count();
        
        $this->info("Total statistics:");
        $this->info("  Total emails: {$total}");
        $this->info("  Pending: {$pending}");
        $this->info("  Sent: {$sentTotal}");
        $this->info("  Failed: {$failedTotal}");
    }
    
    /**
     * Send single email using Laravel Mail with SMTP
     */
    private function sendSingleEmail($email)
    {
        try {
            Mail::to($email)->send(new MassEmailMail($email));
            return true;
        } catch (\Exception $e) {
            throw new \Exception('Laravel Mail failed: ' . $e->getMessage());
        }
    }
}
