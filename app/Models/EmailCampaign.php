<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailCampaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'template',
        'status',
        'scheduled_at',
        'sent_at',
        'recipients_count',
        'sent_count',
        'failed_count',
        'batch_size',
        'delay_between_batches',
        'emails_per_minute',
        'next_batch_at',
        'current_batch',
        'total_batches',
        'user_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'next_batch_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(EmailLog::class, 'campaign_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function getSuccessRateAttribute()
    {
        if ($this->sent_count == 0) {
            return 0;
        }
        
        return round(($this->sent_count - $this->failed_count) / $this->sent_count * 100, 2);
    }
}
