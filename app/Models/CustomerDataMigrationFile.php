<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\ImplementerTicketReply;

class CustomerDataMigrationFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'customer_id',
        'section',
        'item',
        'version',
        'file_name',
        'file_path',
        'remark',
        'implementer_remark',
        'status',
        'uploaded_by_type',
        'uploaded_by_user_id',
        'source_ticket_reply_id',
        'source_attachment_path',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function uploadedByUser()
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function sourceTicketReply()
    {
        return $this->belongsTo(ImplementerTicketReply::class, 'source_ticket_reply_id');
    }

    public function scopeFromCustomer($query)
    {
        return $query->where('uploaded_by_type', 'customer');
    }

    public function scopeFromImplementer($query)
    {
        return $query->where('uploaded_by_type', 'implementer');
    }

    public static function nextVersion(int $leadId, string $section, string $item): int
    {
        $max = static::where('lead_id', $leadId)
            ->where('section', $section)
            ->where('item', $item)
            ->max('version');

        return ($max ?? 0) + 1;
    }
}
