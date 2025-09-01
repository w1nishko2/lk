<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'email',
        'name',
        'status',
        'error_message',
        'sent_at',
        'attempts',
        'variables'
    ];

    protected $casts = [
        'variables' => 'array',
        'sent_at' => 'datetime'
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'campaign_id');
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed(string $error): void
    {
        $this->increment('attempts');
        $this->update([
            'status' => 'failed',
            'error_message' => $error
        ]);
    }
}
