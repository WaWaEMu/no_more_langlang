<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class FosterVenue extends Model
{
    use HasFactory;

    // Type constants (Tags)
    public const TYPE_HAIR_SALON = 'hair_salon';
    public const TYPE_VET_CLINIC = 'vet_clinic';
    public const TYPE_PET_SUPPLIES = 'pet_supplies';
    public const TYPE_PET_GROOMING = 'pet_grooming';
    public const TYPE_PET_HOTEL = 'pet_hotel';
    public const TYPE_RESTAURANT_CAFE = 'restaurant_cafe';
    public const TYPE_PUBLIC_SHELTER = 'public_shelter';
    public const TYPE_PRIVATE_SHELTER = 'private_shelter';
    public const TYPE_STUDIO = 'studio';
    public const TYPE_LEISURE_HYBRID = 'leisure_hybrid';


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
        'type' => 'array',
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
            $city = str_replace('台', '臺', $filters['city']);
            $query->where('city', 'LIKE', "%{$city}%");
        }
        if (!empty($filters['district'])) {
            $district = str_replace('台', '臺', $filters['district']);
            $query->where('district', 'LIKE', "%{$district}%");
        }
        if (!empty($filters['type'])) {
            $query->whereJsonContains('type', $filters['type']);
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
