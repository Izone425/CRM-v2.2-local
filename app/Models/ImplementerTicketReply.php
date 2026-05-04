<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ImplementerTicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'implementer_ticket_id',
        'sender_type',
        'sender_id',
        'email_template_id',
        'thread_label',
        'message',
        'attachments',
        'is_internal_note',
    ];

    protected $casts = [
        'attachments' => 'array',
        'is_internal_note' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(ImplementerTicket::class, 'implementer_ticket_id');
    }

    public function sender(): MorphTo
    {
        return $this->morphTo();
    }

    public function getSenderNameAttribute(): string
    {
        return $this->sender?->name ?? 'Unknown';
    }

    public function getSenderTypeLabel(): string
    {
        return match ($this->sender_type) {
            'App\Models\Customer' => 'Customer',
            'App\Models\User' => 'Staff',
            default => 'Unknown',
        };
    }
}
