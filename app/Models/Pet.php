<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    // Status constants
    public const STATUS_AVAILABLE = 'available'; // 開放領養
    public const STATUS_PAUSED = 'paused'; // 暫停送養
    public const STATUS_PENDING = 'pending'; // 預約互動
    public const STATUS_ADOPTED = 'adopted'; // 送養成功

    protected $fillable = [
        'user_id',
        'title',
        'city',
        'town',
        'is_stray',
        'type',
        'color',
        'fur_type',
        'name',
        'gender',
        'age',
        'is_neuter',
        'status'
    ];

    protected $appends = ['sendable_cities'];

    public function getSendableCitiesAttribute()
    {
        return $this->sendableCities()->pluck('city')->toArray();
    }

    public function sendableCities()
    {
        return $this->hasMany(PetSendableCity::class);
    }

    public function images()
    {
        return $this->hasMany(PetImage::class)->orderBy('type')->orderBy('index');
    }

    // Each pet has a record, so the hasOne method is used, and the function name is singular
    public function detail()
    {
        return $this->hasOne(PetDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attachSendableCities(array $cities): void
    {
        foreach ($cities as $city) {
            $this->sendableCities()->create([
                'city' => $city
            ]);
        }
    }

    public function storeImages(array $images): void
    {
        $index = 0;
        $date = now()->format('Y_m_d');

        foreach ($images as $image) {
            $extension = $image->guessExtension();
            $filename = $this->id . '_' . $index . '.' . $extension;
            $path = $image->storeAs("images/{$date}", $filename, 'public');
            $this->images()->create([
                'type' => 'preview',
                'index' => $index,
                'path' => $path ?: null
            ]);

            $index++;
        }
    }

    public function storeDetail(array $details): void
    {
        $this->detail()->create($details);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(PetComment::class)->orderBy('created_at', 'desc');
    }

    public function adoptionApplications()
    {
        return $this->hasMany(AdoptionApplication::class);
    }

    public function adoptionCase()
    {
        return $this->hasOne(AdoptionCase::class);
    }

    public function scopeFilter($query, array $filters)
    {
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }
        if (isset($filters['city'])) {
            $query->where('city', $filters['city']);
        }
        if (isset($filters['color'])) {
            $query->where('color', $filters['color']);
        }
        if (isset($filters['fur_type'])) {
            $query->where('fur_type', $filters['fur_type']);
        }
        if (isset($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }
        if (isset($filters['age'])) {
            $query->where('age', $filters['age']);
        }
        if (isset($filters['is_neuter'])) {
            $isNeuter = $filters['is_neuter'];
            $query->where('is_neuter', $isNeuter === 'true' || $isNeuter === '1' || $isNeuter === true || $isNeuter === 1);
        }
        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }
    }

    public function scopeSearch($query, $keyword)
    {
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('city', 'like', "%{$keyword}%")
                    ->orWhere('town', 'like', "%{$keyword}%")
                    ->orWhereHas('detail', function ($dq) use ($keyword) {
                        $dq->where('adoption_description', 'like', "%{$keyword}%")
                            ->orWhere('health_description', 'like', "%{$keyword}%")
                            ->orWhere('adoption_condition', 'like', "%{$keyword}%");
                    });
            });
        }
    }

    public static function getAvailablePets(array $filters, int $perPage)
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        return self::with(['images', 'detail', 'user'])
            ->withExists([
                'favoritedBy as is_favorite' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->filter($filters)
            ->search($filters['keyword'] ?? null)
            ->latest()
            ->paginate($perPage);
    }

    public static function getUserPets(int $userId)
    {
        return self::where('user_id', $userId)
            ->with(['images', 'user', 'adoptionCase.adopter'])
            ->latest()
            ->get();
    }

    public static function getUserFavorites(int $userId)
    {
        return User::findOrFail($userId)
            ->favorites()
            ->with(['images', 'user'])
            ->withExists([
                'favoritedBy as is_favorite' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                }
            ])
            ->orderByPivot('created_at', 'desc')
            ->get();
    }
}
