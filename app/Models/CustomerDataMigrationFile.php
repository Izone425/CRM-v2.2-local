<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
