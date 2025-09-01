<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'subject_template',
        'html_content',
        'text_content',
        'preview_text',
        'variables',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean'
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class, 'template_id');
    }

    public function renderSubject(array $variables = []): string
    {
        return $this->renderTemplate($this->subject_template, $variables);
    }

    public function renderHtml(array $variables = []): string
    {
        return $this->renderTemplate($this->html_content, $variables);
    }

    public function renderText(array $variables = []): string
    {
        return $this->renderTemplate($this->text_content, $variables);
    }

    private function renderTemplate(string $template, array $variables): string
    {
        $content = $template;
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }
        return $content;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
