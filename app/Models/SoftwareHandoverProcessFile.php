<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftwareHandoverProcessFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'uploaded_by',
        'version',
        'file_name',
        'file_path',
        'remark',
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public static function nextVersion(int $leadId): int
    {
        $max = static::where('lead_id', $leadId)->max('version');

        return ($max ?? 0) + 1;
    }

    public static function latestForLead(int $leadId)
    {
        return static::where('lead_id', $leadId)->orderBy('version', 'desc')->first();
    }
}
