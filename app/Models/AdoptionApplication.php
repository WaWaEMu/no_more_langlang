<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pet_id',
        'name',
        'phone',
        'line_id',
        'housing_type',
        'experience',
        'family_agreement',
        'message',
        'owner_message',
        'status',
    ];

    protected $casts = [
        'family_agreement' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class);
    }

    /**
     * Get applications sent by the user.
     */
    public static function getSentByUser(int $userId)
    {
        return self::where('user_id', $userId)
            ->with([
                'pet' => function ($query) {
                    $query->with([
                        'images' => function ($q) {
                            $q->limit(1);
                        }
                    ]);
                }
            ])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get applications received for the user's pets, grouped by pet.
     */
    public static function getReceivedGroupedByPet(int $userId)
    {
        return Pet::where('user_id', $userId)
            ->with([
                'images' => function ($query) {
                    $query->limit(1);
                }
            ])
            ->withCount('adoptionApplications')
            ->having('adoption_applications_count', '>', 0)
            ->get()
            ->map(function ($pet) {
                $applications = $pet->adoptionApplications()
                    ->with('user:id,name')
                    ->orderBy('created_at', 'desc')
                    ->get();

                return [
                    'pet' => [
                        'id' => $pet->id,
                        'name' => $pet->name,
                        'image' => $pet->images->first()?->path,
                    ],
                    'applications' => $applications->map(function ($app) {
                        return [
                            'id' => $app->id,
                            'applicant_name' => $app->name,
                            'applicant_user' => $app->user ? [
                                'id' => $app->user->id,
                                'name' => $app->user->name,
                            ] : null,
                            'phone' => $app->phone,
                            'line_id' => $app->line_id,
                            'housing_type' => $app->housing_type,
                            'experience' => $app->experience,
                            'family_agreement' => $app->family_agreement,
                            'message' => $app->message,
                            'owner_message' => $app->owner_message,
                            'status' => $app->status,
                            'created_at' => $app->created_at,
                            'updated_at' => $app->updated_at,
                        ];
                    }),
                ];
            });
    }
}
