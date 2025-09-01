<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Exception\TransportException;

class EmailDeliveryService
{
    private array $alternativeProviders = [
        'gmail' => [
            'host' => 'smtp.gmail.com',
            'port' => 587,
            'encryption' => 'tls'
        ],
        'yandex' => [
            'host' => 'smtp.yandex.ru', 
            'port' => 587,
            'encryption' => 'tls'
        ],
        'mailru' => [
            'host' => 'smtp.mail.ru',
            'port' => 587,
            'encryption' => 'tls'
        ],
        'sendmail' => [
            'mailer' => 'sendmail'
        ]
    ];

    public function isAvailable(): bool
    {
        $currentMailer = config('mail.default');
        
        if ($currentMailer === 'sendmail') {
            return $this->checkSendmailAvailable();
        }
        
        return $this->checkSmtpConnection();
    }

    public function checkSmtpConnection(): bool
    {
        $host = config('mail.mailers.smtp.host');
        $port = config('mail.mailers.smtp.port');
        
        if (empty($host) || empty($port)) {
            return false;
        }

        try {
            $connection = @fsockopen($host, $port, $errno, $errstr, 5);
            if ($connection) {
                fclose($connection);
                return true;
            }
        } catch (\Exception $e) {
            Log::warning('SMTP connection test failed', [
                'host' => $host,
                'port' => $port,
                'error' => $e->getMessage()
            ]);
        }
        
        return false;
    }

    public function checkSendmailAvailable(): bool
    {
        return function_exists('mail') || is_executable('/usr/sbin/sendmail');
    }

    public function switchToAlternativeProvider(string $provider): bool
    {
        if (!isset($this->alternativeProviders[$provider])) {
            return false;
        }

        $config = $this->alternativeProviders[$provider];
        
        if ($provider === 'sendmail') {
            Config::set('mail.default', 'sendmail');
        } else {
            Config::set([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => $config['host'],
                'mail.mailers.smtp.port' => $config['port'],
                'mail.mailers.smtp.encryption' => $config['encryption']
            ]);
        }

        Log::info('Switched to alternative email provider', [
            'provider' => $provider,
            'config' => $config
        ]);

        return true;
    }

    public function testConnection(): array
    {
        $results = [
            'current' => $this->testCurrentProvider(),
            'alternatives' => []
        ];

        foreach (array_keys($this->alternativeProviders) as $provider) {
            $results['alternatives'][$provider] = $this->testProvider($provider);
        }

        return $results;
    }

    private function testCurrentProvider(): array
    {
        $mailer = config('mail.default');
        
        return [
            'mailer' => $mailer,
            'available' => $this->isAvailable(),
            'config' => $mailer === 'smtp' ? [
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'encryption' => config('mail.mailers.smtp.encryption')
            ] : ['path' => config('mail.mailers.sendmail.path')]
        ];
    }

    private function testProvider(string $provider): array
    {
        if (!isset($this->alternativeProviders[$provider])) {
            return ['available' => false, 'error' => 'Provider not configured'];
        }

        $config = $this->alternativeProviders[$provider];
        
        if ($provider === 'sendmail') {
            return [
                'available' => $this->checkSendmailAvailable(),
                'config' => $config
            ];
        }

        try {
            $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
            if ($connection) {
                fclose($connection);
                return ['available' => true, 'config' => $config];
            }
        } catch (\Exception $e) {
            return [
                'available' => false, 
                'error' => $e->getMessage(),
                'config' => $config
            ];
        }
        
        return ['available' => false, 'error' => 'Connection failed', 'config' => $config];
    }

    public function getRecommendedProvider(): ?string
    {
        // Сначала проверяем текущий провайдер
        if ($this->isAvailable()) {
            return null; // текущий провайдер работает
        }

        // Проверяем альтернативные провайдеры по приоритету
        $priority = ['sendmail', 'gmail', 'yandex', 'mailru'];
        
        foreach ($priority as $provider) {
            if ($this->testProvider($provider)['available']) {
                return $provider;
            }
        }

        return null;
    }
}
