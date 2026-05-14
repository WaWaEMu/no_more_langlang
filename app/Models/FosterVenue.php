<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FosterVenue extends Model
{
    use HasFactory;

    // Type constants
    public const TYPE_RESTAURANT = 'restaurant';
    public const TYPE_SHELTER = 'shelter';
    public const TYPE_VET_CLINIC = 'vet_clinic';
    public const TYPE_HAIR_SALON = 'hair_salon';
    public const TYPE_BOARD_GAME = 'board_game';
    public const TYPE_PET_STORE = 'pet_store';
    public const TYPE_OTHER = 'other';


    // Status constants
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_PENDING = 'pending';

    protected $fillable = [
        'uuid',
        'name',
        'type',
        'primary_type_display_name',
        'description',
        'phone',
        'city',
        'district',
        'address',
        'latitude',
        'longitude',
        'rating',
        'user_rating_count',
        'business_hours',
        'website_url',
        'facebook_url',
        'instagram_url',
        'line_id',
        'pet_types',
        'services',
        'is_verified',
        'status',
        'business_status',
        'user_id',
    ];

    protected $casts = [
        'business_hours' => 'array',
        'pet_types' => 'array',
        'services' => 'array',
        'latitude' => 'float',
        'longitude' => 'float',
        'rating' => 'float',
        'user_rating_count' => 'integer',
        'is_verified' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($venue) {
            if (empty($venue->uuid)) {
                $venue->uuid = Str::random(12);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function images()
    {
        return $this->hasMany(FosterVenueImage::class)->orderBy('type')->orderBy('index');
    }

    public function coverImage()
    {
        return $this->hasOne(FosterVenueImage::class)->where('type', 'cover');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        if (!empty($filters['district'])) {
            $query->where('district', $filters['district']);
        }
        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['pet_type'])) {
            $query->whereJsonContains('pet_types', $filters['pet_type']);
        }
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
