<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailSubscriber extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'first_name',
        'last_name', 
        'status',
        'subscribed_at',
        'unsubscribed_at',
        'bounce_count',
        'tags',
        'metadata'
    ];

    protected $casts = [
        'subscribed_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'tags' => 'array',
        'metadata' => 'array'
    ];

    const STATUS_ACTIVE = 'active';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';
    const STATUS_BOUNCED = 'bounced';
    const STATUS_COMPLAINED = 'complained';

    public function logs()
    {
        return $this->hasMany(EmailLog::class, 'subscriber_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeByTag($query, string $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function addTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->update(['tags' => $tags]);
        }
    }

    public function removeTag(string $tag): void
    {
        $tags = $this->tags ?? [];
        $tags = array_filter($tags, fn($t) => $t !== $tag);
        $this->update(['tags' => array_values($tags)]);
    }

    public function unsubscribe(): void
    {
        $this->update([
            'status' => self::STATUS_UNSUBSCRIBED,
            'unsubscribed_at' => now()
        ]);
    }
}
