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
        'content',
        'template',
        'status',
        'total_recipients',
        'sent_count',
        'failed_count',
        'delay_seconds',
        'scheduled_at',
        'started_at',
        'completed_at',
        'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function recipients(): HasMany
    {
        return $this->hasMany(EmailRecipient::class, 'campaign_id');
    }

    public function pendingRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'pending');
    }

    public function sentRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'sent');
    }

    public function failedRecipients(): HasMany
    {
        return $this->recipients()->where('status', 'failed');
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->total_recipients === 0) {
            return 0;
        }
        
        return round(($this->sent_count / $this->total_recipients) * 100, 2);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isSending(): bool
    {
        return $this->status === 'sending';
    }

    public function canStart(): bool
    {
        return in_array($this->status, ['draft', 'paused']) && $this->total_recipients > 0;
    }
}
