<?php

namespace App\Models;

use App\Enums\ImplementerTicketStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImplementerTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'customer_id',
        'implementer_user_id',
        'implementer_name',
        'lead_id',
        'software_handover_id',
        'subject',
        'description',
        'status',
        'priority',
        'category',
        'module',
        'attachments',
        'closed_at',
        'closed_by',
        'closed_by_type',
        'first_responded_at',
    ];

    protected $casts = [
        'status' => ImplementerTicketStatus::class,
        'attachments' => 'array',
        'closed_at' => 'datetime',
        'first_responded_at' => 'datetime',
    ];

    // SLA Constants
    const SLA_HOURS = 48;
    const FIRST_RESPONSE_SLA_HOURS = 24;

    protected static function booted(): void
    {
        static::created(function (ImplementerTicket $ticket) {
            $year = $ticket->created_at ? $ticket->created_at->format('y') : date('y');
            $ticket->update([
                'ticket_number' => 'IMP-' . $year . str_pad($ticket->id, 4, '0', STR_PAD_LEFT),
            ]);
        });
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function implementerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'implementer_user_id');
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function softwareHandover(): BelongsTo
    {
        return $this->belongsTo(SoftwareHandover::class);
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ImplementerTicketReply::class)->orderBy('created_at', 'asc');
    }

    public function getFormattedTicketNumberAttribute(): string
    {
        if ($this->ticket_number) {
            return $this->ticket_number;
        }

        $year = $this->created_at ? $this->created_at->format('y') : date('y');
        return 'IMP-' . $year . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Resolve the implementer User for a given customer.
     */
    public static function resolveImplementerForCustomer(Customer $customer): ?array
    {
        $lead = $customer->lead;
        if (!$lead) {
            return null;
        }

        // Priority 1: SoftwareHandover implementer
        $handover = SoftwareHandover::where('lead_id', $lead->id)
            ->whereNotNull('implementer')
            ->latest()
            ->first();

        if ($handover && $handover->implementer) {
            $user = User::where('name', $handover->implementer)->first();
            if ($user) {
                return [
                    'user' => $user,
                    'name' => $handover->implementer,
                    'lead_id' => $lead->id,
                    'software_handover_id' => $handover->id,
                ];
            }
        }

        // Priority 2: ImplementerAppointment implementer
        $appointment = ImplementerAppointment::where('lead_id', $lead->id)
            ->whereNotNull('implementer')
            ->latest()
            ->first();

        if ($appointment && $appointment->implementer) {
            $user = User::where('name', $appointment->implementer)->first();
            if ($user) {
                return [
                    'user' => $user,
                    'name' => $appointment->implementer,
                    'lead_id' => $lead->id,
                    'software_handover_id' => $handover?->id,
                ];
            }
        }

        return null;
    }

    public function getSlaDeadline(): Carbon
    {
        return $this->created_at->copy()->addHours(self::SLA_HOURS);
    }

    public function isOverdue(): bool
    {
        if ($this->status === ImplementerTicketStatus::CLOSED) {
            return false;
        }
        return now()->gt($this->getSlaDeadline());
    }

    public function getSlaStatus(): string
    {
        if ($this->status === ImplementerTicketStatus::CLOSED) {
            return 'resolved';
        }

        $deadline = $this->getSlaDeadline();
        $hoursRemaining = now()->diffInHours($deadline, false);

        if ($hoursRemaining < 0) {
            return 'overdue';
        }
        if ($hoursRemaining <= 12) {
            return 'at_risk';
        }
        return 'on_track';
    }

    public function getTimeRemaining(): string
    {
        if ($this->status === ImplementerTicketStatus::CLOSED) {
            return 'Resolved';
        }

        $deadline = $this->getSlaDeadline();
        $diff = now()->diff($deadline);

        if (now()->gt($deadline)) {
            $hours = now()->diffInHours($deadline);
            return 'Overdue';
        }

        $hours = now()->diffInHours($deadline);
        if ($hours >= 24) {
            $days = floor($hours / 24);
            return $days . 'd ' . ($hours % 24) . 'h left';
        }
        return $hours . 'h left';
    }

    public function wasResolvedWithinSla(): bool
    {
        if (!$this->closed_at) {
            return false;
        }
        return $this->closed_at->lte($this->getSlaDeadline());
    }

    public function wasFirstResponseWithinSla(): bool
    {
        if (!$this->first_responded_at) {
            return false;
        }
        $responseDeadline = $this->created_at->copy()->addHours(self::FIRST_RESPONSE_SLA_HOURS);
        return $this->first_responded_at->lte($responseDeadline);
    }
}
