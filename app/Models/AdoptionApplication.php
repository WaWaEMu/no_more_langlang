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
}
