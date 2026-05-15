<?php

namespace App\Models;

use App\Enums\ImplementerTicketStatus;
use App\Models\SlaConfiguration;
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
        'is_kickoff_thread',
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
        'pending_client_since',
        'followup_sent_at',
        'is_overdue',
        'merged_into_ticket_id',
        'merged_at',
        'merged_by',
    ];

    protected $casts = [
        'status' => ImplementerTicketStatus::class,
        'attachments' => 'array',
        'closed_at' => 'datetime',
        'first_responded_at' => 'datetime',
        'pending_client_since' => 'datetime',
        'followup_sent_at' => 'datetime',
        'is_overdue' => 'boolean',
        'is_kickoff_thread' => 'boolean',
        'merged_at' => 'datetime',
    ];

    // SLA Constants
    const SLA_HOURS = 48;
    const FIRST_RESPONSE_SLA_HOURS = 24;

    protected static function booted(): void
    {
        static::created(function (ImplementerTicket $ticket) {
            if (!empty($ticket->ticket_number)) {
                return; // already set (e.g. by a migration backfill)
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($ticket) {
                if ($ticket->software_handover_id && $ticket->softwareHandover) {
                    $sequence = self::nextSequenceForHandover($ticket);
                    $padded = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
                    $ticket->ticket_number = "{$ticket->softwareHandover->project_code}_{$padded}";
                } else {
                    $year = $ticket->created_at ? $ticket->created_at->format('y') : date('y');
                    $idPadded = str_pad((string) $ticket->id, 4, '0', STR_PAD_LEFT);
                    $ticket->ticket_number = "IMP-{$year}{$idPadded}";
                }
                $ticket->saveQuietly();
            });
        });
    }

    /**
     * Compute the next per-handover sequence number for this ticket.
     *
     * Slot 1 is reserved for the Kick-Off auto-thread; manual tickets start at 2
     * even when no Kick-Off thread exists yet. Wrap calls in a transaction with
     * lockForUpdate() so concurrent creates serialise.
     */
    private static function nextSequenceForHandover(ImplementerTicket $ticket): int
    {
        if ($ticket->is_kickoff_thread) {
            return 1;
        }

        $existingNumbers = self::where('software_handover_id', $ticket->software_handover_id)
            ->where('id', '!=', $ticket->id)
            ->lockForUpdate()
            ->pluck('ticket_number')
            ->filter()
            ->all();

        $maxSeq = 1;
        foreach ($existingNumbers as $num) {
            $tail = substr($num, strrpos($num, '_') + 1);
            if (ctype_digit($tail)) {
                $maxSeq = max($maxSeq, (int) $tail);
            }
        }

        return max(2, $maxSeq + 1);
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

    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_ticket_id');
    }

    public function mergedFrom(): HasMany
    {
        return $this->hasMany(self::class, 'merged_into_ticket_id');
    }

    public function mergedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'merged_by');
    }

    public function isMerged(): bool
    {
        return !is_null($this->merged_into_ticket_id);
    }

    public function getFormattedTicketNumberAttribute(): string
    {
        if ($this->ticket_number) {
            return $this->ticket_number;
        }

        if ($this->software_handover_id && $this->softwareHandover) {
            $sequence = $this->is_kickoff_thread ? 1 : max(2, (int) $this->id);
            $padded = str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
            return $this->softwareHandover->project_code . '_' . $padded;
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
        $hours = SlaConfiguration::current()->resolution_sla_hours ?? self::SLA_HOURS;
        return $this->created_at->copy()->addHours($hours);
    }

    public function getFirstReplyDeadline(): ?Carbon
    {
        $config = SlaConfiguration::current();
        if (!$config->first_reply_enabled) {
            return null;
        }

        $created = $this->created_at->copy()->setTimezone(config('app.timezone'));
        $cutoff = $config->first_reply_cutoff_time;

        if ($created->format('H:i') <= $cutoff && $config->isWorkingDay($created)) {
            return $created->copy()->endOfDay();
        }

        return $config->addWorkingDays($created, 1)->endOfDay();
    }

    public function isOverdue(): bool
    {
        if ($this->status === ImplementerTicketStatus::CLOSED) {
            return false;
        }
        // Check flag set by scheduled command (first-reply-deadline overdue)
        if ($this->is_overdue) {
            return true;
        }
        return now()->gt($this->getSlaDeadline());
    }

    public function getSlaStatus(): string
    {
        if ($this->status === ImplementerTicketStatus::CLOSED) {
            return 'resolved';
        }

        if ($this->is_overdue) {
            return 'overdue';
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

        if (now()->gt($deadline)) {
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
        $hours = SlaConfiguration::current()->first_response_sla_hours ?? self::FIRST_RESPONSE_SLA_HOURS;
        $responseDeadline = $this->created_at->copy()->addHours($hours);
        return $this->first_responded_at->lte($responseDeadline);
    }

    /**
     * Customer-portal-friendly status key derived from the raw enum + reply direction.
     *
     * Returns one of: 'open', 'awaiting_reply', 'in_progress', 'closed'.
     * - closed                              → 'closed'
     * - pending_support | pending_rnd       → 'in_progress'
     * - pending_client                      → 'open'   (ball in client's court)
     * - open + last reply by Customer       → 'awaiting_reply'
     * - open + last reply by User (impl)    → 'open'
     * - open + no replies                   → 'awaiting_reply'   (assume customer-created)
     *
     * Uses the already-loaded `replies` relation when present to avoid N+1.
     */
    public function customerFacingStatus(): string
    {
        if ($this->status === ImplementerTicketStatus::CLOSED) {
            return 'closed';
        }
        if (in_array($this->status, [ImplementerTicketStatus::PENDING_SUPPORT, ImplementerTicketStatus::PENDING_RND], true)) {
            return 'in_progress';
        }
        if ($this->status === ImplementerTicketStatus::PENDING_CLIENT) {
            return 'open';
        }

        if ($this->relationLoaded('replies')) {
            $lastReply = $this->replies
                ->filter(fn ($r) => !$r->is_internal_note)
                ->sortByDesc('created_at')
                ->first();
        } else {
            $lastReply = $this->replies()
                ->where('is_internal_note', false)
                ->latest('created_at')
                ->first();
        }

        if (!$lastReply) {
            return 'awaiting_reply';
        }

        return $lastReply->sender_type === Customer::class ? 'awaiting_reply' : 'open';
    }

    /**
     * Count tickets that the customer sees as "Open" for a given lead.
     * Mirrors the "Open Tickets" stat card in CustomerImplementerThread so the
     * customer-portal sidebar badge and that card share one source of truth.
     */
    public static function customerOpenCountForLead(int $leadId): int
    {
        return static::where('lead_id', $leadId)
            ->whereNull('merged_into_ticket_id')
            ->whereIn('status', [
                ImplementerTicketStatus::OPEN,
                ImplementerTicketStatus::PENDING_CLIENT,
            ])
            ->with(['replies' => fn ($q) => $q->where('is_internal_note', false)->orderBy('created_at', 'asc')])
            ->get()
            ->filter(fn ($t) => $t->customerFacingStatus() === 'open')
            ->count();
    }
}
