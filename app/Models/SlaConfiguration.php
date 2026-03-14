<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SlaConfiguration extends Model
{
    protected $fillable = [
        'first_reply_cutoff_time',
        'first_reply_enabled',
        'followup_reminder_days',
        'followup_auto_close_days',
        'followup_enabled',
        'business_start_time',
        'business_end_time',
        'resolution_sla_hours',
        'first_response_sla_hours',
        'updated_by',
    ];

    protected $casts = [
        'first_reply_enabled' => 'boolean',
        'followup_enabled' => 'boolean',
        'followup_reminder_days' => 'integer',
        'followup_auto_close_days' => 'integer',
        'resolution_sla_hours' => 'integer',
        'first_response_sla_hours' => 'integer',
    ];

    /**
     * Get the current (single-row) SLA configuration, cached for 5 minutes.
     */
    public static function current(): self
    {
        return Cache::remember('sla_configuration', 300, function () {
            return self::first() ?? new self([
                'first_reply_cutoff_time' => '17:30',
                'first_reply_enabled' => true,
                'followup_reminder_days' => 3,
                'followup_auto_close_days' => 2,
                'followup_enabled' => true,
                'business_start_time' => '08:00',
                'business_end_time' => '18:00',
                'resolution_sla_hours' => 48,
                'first_response_sla_hours' => 24,
            ]);
        });
    }

    /**
     * Clear the cached configuration after saving.
     */
    public static function clearCache(): void
    {
        Cache::forget('sla_configuration');
    }

    /**
     * Check if a given date is a working day (not weekend, not public holiday).
     */
    public function isWorkingDay(Carbon $date): bool
    {
        if ($date->isWeekend()) {
            return false;
        }

        $dateStr = $date->toDateString();

        $isPublicHoliday = PublicHoliday::where('date', $dateStr)->exists();
        if ($isPublicHoliday) {
            return false;
        }

        $isCustomHoliday = CustomPublicHoliday::where('date', $dateStr)->exists();
        if ($isCustomHoliday) {
            return false;
        }

        return true;
    }

    /**
     * Add N working days to a date, skipping weekends and holidays.
     */
    public function addWorkingDays(Carbon $from, int $days): Carbon
    {
        $current = $from->copy();
        $added = 0;

        while ($added < $days) {
            $current->addDay();
            if ($this->isWorkingDay($current)) {
                $added++;
            }
        }

        return $current;
    }

    /**
     * Count working days between two dates (exclusive of start, inclusive of end).
     */
    public function countWorkingDaysBetween(Carbon $from, Carbon $to): int
    {
        $count = 0;
        $current = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($current->lt($end)) {
            $current->addDay();
            if ($this->isWorkingDay($current)) {
                $count++;
            }
        }

        return $count;
    }

    /**
     * Optimized: check working day using pre-fetched holiday set.
     */
    public static function isWorkingDayWithHolidays(Carbon $date, array $holidays): bool
    {
        if ($date->isWeekend()) {
            return false;
        }
        return !in_array($date->toDateString(), $holidays);
    }

    /**
     * Pre-fetch all holidays for a date range (for batch operations).
     */
    public static function fetchHolidaysForRange(string $startDate, string $endDate): array
    {
        $public = PublicHoliday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        $custom = CustomPublicHoliday::whereBetween('date', [$startDate, $endDate])
            ->pluck('date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        return array_unique(array_merge($public, $custom));
    }

    /**
     * Count working days using pre-fetched holidays (optimized for batch).
     */
    public static function countWorkingDaysWithHolidays(Carbon $from, Carbon $to, array $holidays): int
    {
        $count = 0;
        $current = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($current->lt($end)) {
            $current->addDay();
            if (self::isWorkingDayWithHolidays($current, $holidays)) {
                $count++;
            }
        }

        return $count;
    }
}
