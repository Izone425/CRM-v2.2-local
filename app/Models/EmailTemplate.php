<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    public const KICK_OFF_TEMPLATE_ID = 17;

    protected $fillable = [
        'name',
        'subject',
        'content',
        'type',
        'category',
        'thread_label',
        'created_by',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeImplementerThread($query)
    {
        return $query->where('type', 'implementer_thread');
    }

    public function scopeSupportThread($query)
    {
        return $query->where('type', 'support_thread');
    }

    public static function availableCategories(): array
    {
        return [
            'First Response' => 'First Response',
            'Follow-up' => 'Follow-up',
            'Escalation' => 'Escalation',
            'General' => 'General',
        ];
    }

    public static function availablePlaceholders(): array
    {
        return [
            '[Client Name]' => 'Customer/client name',
            '[Ticket ID]' => 'Ticket number (e.g. IMP-2501)',
            '[Implementer Name]' => 'Assigned implementer name',
            '[Company Name]' => 'Customer company name',
            '[Category]' => 'Ticket category',
            '[Module]' => 'Ticket module',
        ];
    }

    /**
     * Replace placeholders in content string with actual data.
     * Uses regex to handle HTML tags/entities that contenteditable editors may inject within brackets.
     */
    public static function replacePlaceholders(string $content, array $data): string
    {
        $placeholders = [
            'Client Name' => $data['client_name'] ?? null,
            'Ticket ID' => $data['ticket_id'] ?? null,
            'Implementer Name' => $data['implementer_name'] ?? null,
            'Company Name' => $data['company_name'] ?? null,
            'Category' => $data['category'] ?? null,
            'Module' => $data['module'] ?? null,
        ];

        foreach ($placeholders as $key => $value) {
            if ($value === null) continue;

            // Escape value to prevent XSS when inserted into HTML content
            $value = e($value);

            // First try simple str_replace (fastest path)
            $simpleMatch = '[' . $key . ']';
            if (str_contains($content, $simpleMatch)) {
                $content = str_replace($simpleMatch, $value, $content);
                continue;
            }

            // Fallback: regex that matches [Key] even with HTML tags/&nbsp; inside
            $escapedKey = preg_quote($key, '/');
            // Allow HTML tags and &nbsp; entities between words
            $flexiblePattern = str_replace('\\ ', '(?:[\\s]|&nbsp;|<[^>]*>)+', $escapedKey);
            $pattern = '/\[(?:<[^>]*>)*' . $flexiblePattern . '(?:<[^>]*>)*\]/u';
            $content = preg_replace($pattern, $value, $content);
        }

        return $content;
    }
}
