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
     */
    public static function calculateNextReportDate(?array $config): ?\Carbon\Carbon
    {
        if (!$config || !isset($config['frequency'])) {
            return null;
        }

        return match ($config['frequency']) {
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            'quarterly' => now()->addMonths(3),
            default => null,
        };
    }
}
