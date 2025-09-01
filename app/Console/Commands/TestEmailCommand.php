<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\MassEmailMail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email : Email address to send test email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test email to check SMTP configuration';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        $this->info("Testing email configuration...");
        $this->info("Sending to: {$email}");
        
        // Показать конфигурацию
        $this->info("Mail configuration:");
        $this->info("  Driver: " . config('mail.default'));
        $this->info("  Host: " . config('mail.mailers.smtp.host'));
        $this->info("  Port: " . config('mail.mailers.smtp.port'));
        $this->info("  Encryption: " . config('mail.mailers.smtp.encryption'));
        $this->info("  Username: " . config('mail.mailers.smtp.username'));
        $this->info("  From: " . config('mail.from.address'));
        
        try {
            Mail::to($email)->send(new MassEmailMail($email));
            $this->info("✅ Test email sent successfully!");
            
            // Проверим логи
            $this->info("\nChecking logs...");
            $logPath = storage_path('logs/laravel.log');
            if (file_exists($logPath)) {
                $logs = file_get_contents($logPath);
                $recentLogs = substr($logs, -2000); // Последние 2000 символов
                if (strpos($recentLogs, 'Failed to authenticate') !== false) {
                    $this->error("❌ Authentication failed - check username/password");
                } elseif (strpos($recentLogs, 'Connection refused') !== false) {
                    $this->error("❌ Connection refused - check host/port");
                } elseif (strpos($recentLogs, 'stream_socket_enable_crypto') !== false) {
                    $this->error("❌ SSL/TLS error - check encryption settings");
                }
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send test email:");
            $this->error("Error: " . $e->getMessage());
            
            // Дополнительная диагностика
            if (strpos($e->getMessage(), 'Authentication') !== false) {
                $this->error("\n🔧 Authentication issue - check MAIL_USERNAME and MAIL_PASSWORD in .env");
            } elseif (strpos($e->getMessage(), 'Connection') !== false) {
                $this->error("\n🔧 Connection issue - check MAIL_HOST and MAIL_PORT in .env");
            } elseif (strpos($e->getMessage(), 'stream_socket_enable_crypto') !== false) {
                $this->error("\n🔧 SSL/TLS issue - try changing MAIL_ENCRYPTION from 'ssl' to 'tls'");
            }
        }
    }
}
