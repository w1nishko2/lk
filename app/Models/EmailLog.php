<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'email',
        'subject',
        'status',
        'sent_at',
        'opened_at',
        'clicked_at',
        'bounced_at',
        'failed_at',
        'error_message',
        'retry_count',
        'tracking_token',
        'metadata'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime', 
        'clicked_at' => 'datetime',
        'bounced_at' => 'datetime',
        'failed_at' => 'datetime',
        'metadata' => 'array'
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_OPENED = 'opened';
    const STATUS_CLICKED = 'clicked';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_FAILED = 'failed';
    const STATUS_COMPLAINED = 'complained';

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(EmailSubscriber::class, 'subscriber_id');
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now()
        ]);
    }

    public function markAsOpened(): void
    {
        if ($this->status === self::STATUS_SENT) {
            $this->update([
                'status' => self::STATUS_OPENED,
                'opened_at' => now()
            ]);
        }
    }

    public function markAsClicked(): void
    {
        $this->update([
            'status' => self::STATUS_CLICKED,
            'clicked_at' => now()
        ]);
        
        // Также помечаем как открытое, если еще не было
        if (!$this->opened_at) {
            $this->update(['opened_at' => now()]);
        }
    }

    public function markAsFailed(string $error): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'failed_at' => now(),
            'error_message' => $error,
            'retry_count' => $this->retry_count + 1
        ]);
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeRetryable($query, int $maxRetries = 3)
    {
        return $query->where('status', self::STATUS_FAILED)
                    ->where('retry_count', '<', $maxRetries);
    }
}
