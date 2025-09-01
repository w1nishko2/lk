<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailTracking extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'email',
        'tracking_id',
        'opened_at',
        'clicked_at',
        'unsubscribed_at',
        'bounced_at',
        'user_agent',
        'ip_address',
        'click_data',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'bounced_at' => 'datetime',
        'click_data' => 'array',
    ];

    /**
     * Get the campaign that owns the tracking record
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }

    /**
     * Get the subscriber that owns the tracking record
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(EmailSubscriber::class);
    }

    /**
     * Check if email was opened
     */
    public function wasOpened(): bool
    {
        return !is_null($this->opened_at);
    }

    /**
     * Check if email was clicked
     */
    public function wasClicked(): bool
    {
        return !is_null($this->clicked_at);
    }

    /**
     * Mark as opened
     */
    public function markAsOpened(string $userAgent = null, string $ipAddress = null): void
    {
        if (!$this->wasOpened()) {
            $this->update([
                'opened_at' => now(),
                'user_agent' => $userAgent,
                'ip_address' => $ipAddress,
            ]);

            // Increment campaign open count
            $this->campaign->increment('opened_count');
        }
    }

    /**
     * Mark as clicked
     */
    public function markAsClicked(array $clickData = []): void
    {
        $this->update([
            'clicked_at' => now(),
            'click_data' => $clickData,
        ]);

        // Increment campaign click count
        $this->campaign->increment('clicked_count');
    }

    /**
     * Mark as unsubscribed
     */
    public function markAsUnsubscribed(): void
    {
        $this->update(['unsubscribed_at' => now()]);
        $this->campaign->increment('unsubscribed_count');
    }

    /**
     * Generate tracking pixel URL
     */
    public static function generateTrackingPixelUrl(string $trackingId): string
    {
        return url("/email/track/open/{$trackingId}");
    }

    /**
     * Generate click tracking URL
     */
    public static function generateClickTrackingUrl(string $trackingId, string $originalUrl): string
    {
        return url("/email/track/click/{$trackingId}") . '?url=' . urlencode($originalUrl);
    }
}
