<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdoptionCase extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active'; // 追蹤中
    public const STATUS_COMPLETED = 'completed'; // 已完成
    public const STATUS_PAUSED = 'paused'; // 已暫停

    public const SOURCE_PLATFORM = 'platform'; // 透過平台送養流程
    public const SOURCE_MANUAL = 'manual'; // 使用者手動建案

    protected $fillable = [
        'pet_id',
        'adopter_id',
        'owner_id',
        'application_id',
        'status',
        'source',
        'tracking_config',
        'next_report_due_at',
        'last_report_at',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'tracking_config' => 'array',
        'next_report_due_at' => 'datetime',
        'last_report_at' => 'datetime',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    public function adopter()
    {
        return $this->belongsTo(User::class, 'adopter_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function application()
    {
        return $this->belongsTo(AdoptionApplication::class, 'application_id');
    }

    public function reports()
    {
        return $this->hasMany(TrackingReport::class)->orderBy('reported_at', 'desc');
    }

    public function diaryEntries()
    {
        return $this->hasMany(CaseDiaryEntry::class)->orderBy('created_at', 'desc');
    }

    public function scopeForAdopter($query, $userId)
    {
        return $query->where('adopter_id', $userId)
            ->with(['pet.images', 'owner']);
    }

    public function scopeForOwner($query, $userId)
    {
        return $query->where('owner_id', $userId)
            ->with(['pet.images', 'adopter']);
    }

    /**
     * Create a manual case (bypassing the adoption application flow).
     */
    public static function createManual(array $data): self
    {
        return self::create([
            'pet_id' => $data['pet_id'],
            'adopter_id' => $data['adopter_id'] ?? null,
            'owner_id' => $data['owner_id'] ?? null,
            'source' => self::SOURCE_MANUAL,
            'status' => self::STATUS_ACTIVE,
            'tracking_config' => $data['tracking_config'] ?? null,
            'started_at' => now(),
            'next_report_due_at' => self::calculateNextReportDate($data['tracking_config'] ?? null),
        ]);
    }

    /**
     * Finalize an adoption: Create case, update pet status, and update application status.
     *
     * @param array $data
     * @param User $owner
     * @return self
     */
    public static function finalize(array $data, User $owner): self
    {
        return DB::transaction(function () use ($data, $owner) {
            // 1. Create the Adoption Case
            $case = self::create([
                'pet_id' => $data['pet_id'],
                'adopter_id' => $data['adopter_id'],
                'owner_id' => $owner->id,
                'application_id' => $data['application_id'] ?? null,
                'status' => self::STATUS_ACTIVE,
                'tracking_config' => $data['tracking_config'],
                'started_at' => now(),
                'next_report_due_at' => self::calculateNextReportDate($data['tracking_config']),
            ]);

            if (!$case) {
                throw new \Exception('Failed to create adoption case.');
            }

            // 2. Update Pet Status to Adopted (Must succeed)
            $petUpdated = Pet::where('id', $data['pet_id'])
                ->where('user_id', $owner->id) // Security double-check
                ->update(['status' => Pet::STATUS_ADOPTED]);

            if (!$petUpdated) {
                throw new \Exception('Failed to update pet status or unauthorized.');
            }

            // 3. Update Adoption Application Status to Approved (if provided)
            if (!empty($data['application_id'])) {
                $appUpdated = AdoptionApplication::where('id', $data['application_id'])
                    ->update(['status' => 'finalized']);

                if (!$appUpdated) {
                    throw new \Exception('Failed to update adoption application status.');
                }
            }

            return $case;
        });
    }

    /**
     * Calculate the next report due date based on tracking config.
     *
     * Supports optional 'tracking_day' and 'tracking_start_month':
     *   - weekly:    tracking_day = 1 (Mon) ~ 7 (Sun)
     *   - monthly:   tracking_day = 1 ~ 31
     *   - quarterly: tracking_start_month = 1~3 (defines cycle), tracking_day = 1 ~ 31
     */
    public static function calculateNextReportDate(?array $config): ?\Carbon\Carbon
    {
        if (!$config || !isset($config['frequency'])) {
            return null;
        }

        $trackingDay = isset($config['tracking_day']) ? (int) $config['tracking_day'] : null;
        $startMonth = isset($config['tracking_start_month']) ? (int) $config['tracking_start_month'] : null;

        return match ($config['frequency']) {
            'weekly' => self::nextWeeklyDate($trackingDay),
            'monthly' => self::nextMonthlyDate(1, $trackingDay),
            'quarterly' => self::nextQuarterlyDate($startMonth, $trackingDay),
            default => null,
        };
    }

    /**
     * Find the next occurrence of a given day-of-week.
     * $dayOfWeek: 1 (Mon) ~ 7 (Sun), ISO standard.
     */
    private static function nextWeeklyDate(?int $dayOfWeek): \Carbon\Carbon
    {
        if ($dayOfWeek === null) {
            return now()->addWeek();
        }

        // ISO: 1=Mon...7=Sun → Carbon: 0=Sun, 1=Mon...6=Sat
        $carbonDay = $dayOfWeek === 7 ? 0 : $dayOfWeek;

        return now()->next($carbonDay)->startOfDay();
    }

    /**
     * Find the target day in the month that is as close as possible in the future.
     * Clamps to the last day of the month for short months.
     */
    private static function nextMonthlyDate(int $monthsAhead, ?int $dayOfMonth): \Carbon\Carbon
    {
        if ($dayOfMonth === null) {
            return now()->addMonths($monthsAhead);
        }

        $now = now();
        $target = $now->copy();

        // If monthsAhead is 1 (Monthly), we check if this month's target day is in the future.
        // If monthsAhead > 1 (e.g. quarterly fallback), we just add the months.
        if ($monthsAhead === 1) {
            $lastDayThisMonth = $target->daysInMonth;
            $checkDay = min($dayOfMonth, $lastDayThisMonth);

            // If the target day in THIS month is in the future, use it.
            if ($checkDay > $now->day) {
                return $target->day($checkDay)->startOfDay();
            }

            // Otherwise, skip to next month as usual
            $target->addMonth();
        } else {
            $target->addMonths($monthsAhead);
        }

        $lastDay = $target->daysInMonth;
        $day = min($dayOfMonth, $lastDay);

        return $target->day($day)->startOfDay();
    }

    /**
     * Find the next quarterly report date based on a cycle starting month.
     * E.g. startMonth=1 → cycle is Jan, Apr, Jul, Oct.
     * Finds the next future month in that cycle and applies the day.
     */
    private static function nextQuarterlyDate(?int $startMonth, ?int $dayOfMonth): \Carbon\Carbon
    {
        if ($startMonth === null) {
            return self::nextMonthlyDate(3, $dayOfMonth);
        }

        $now = now();
        $currentMonth = $now->month;
        $currentDay = $now->day;

        // Generate the 4 months in this cycle for the current year and next year
        $cycleMonths = [];
        for ($i = 0; $i < 4; $i++) {
            $m = $startMonth + ($i * 3);
            // Normalize to 1-12 range
            $m = (($m - 1) % 12) + 1;
            $cycleMonths[] = $m;
        }

        // Find the next future month in the cycle
        $targetYear = $now->year;
        $targetMonth = null;

        foreach ($cycleMonths as $m) {
            if ($m > $currentMonth) {
                $targetMonth = $m;
                break;
            } elseif ($m === $currentMonth) {
                // Same month: check if the day is still in the future
                $checkDay = $dayOfMonth ? min($dayOfMonth, cal_days_in_month(CAL_GREGORIAN, $m, $targetYear)) : $currentDay + 1;
                if ($checkDay > $currentDay) {
                    $targetMonth = $m;
                    break;
                }
            }
        }

        // If no future month found this year, take the first month of the cycle next year
        if ($targetMonth === null) {
            $targetMonth = $cycleMonths[0];
            $targetYear++;
        }

        $target = \Carbon\Carbon::create($targetYear, $targetMonth, 1);
        $lastDay = $target->daysInMonth;
        $day = $dayOfMonth ? min($dayOfMonth, $lastDay) : 1;

        return $target->day($day)->startOfDay();
    }
}
