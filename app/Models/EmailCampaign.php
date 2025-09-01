<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject', 
        'preview_text',
        'template_id',
        'sender_name',
        'sender_email',
        'status',
        'scheduled_at',
        'started_at',
        'completed_at',
        'total_recipients',
        'sent_count',
        'failed_count',
        'opened_count',
        'clicked_count',
        'unsubscribed_count',
        'bounce_count',
        'settings'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime', 
        'completed_at' => 'datetime',
        'settings' => 'array'
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_PAUSED = 'paused';

    public function logs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    public function subscribers(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'campaign_id')
                    ->join('email_subscribers', 'email_logs.subscriber_id', '=', 'email_subscribers.id');
    }

    public function getSuccessRateAttribute(): float
    {
        if ($this->total_recipients == 0) return 0;
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public function getOpenRateAttribute(): float
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->opened_count / $this->sent_count) * 100, 2);
    }

    public function getClickRateAttribute(): float
    {
        if ($this->sent_count == 0) return 0;
        return round(($this->clicked_count / $this->sent_count) * 100, 2);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', [self::STATUS_SCHEDULED, self::STATUS_SENDING]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }
}
