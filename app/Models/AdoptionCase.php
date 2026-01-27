<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdoptionCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'adopter_id',
        'owner_id',
        'application_id',
        'status',
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
}
