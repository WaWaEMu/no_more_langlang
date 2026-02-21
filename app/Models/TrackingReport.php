<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrackingReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'adoption_case_id',
        'reporter_id',
        'content',
        'images',
        'reported_at',
    ];

    protected $casts = [
        'images' => 'array',
        'reported_at' => 'datetime',
    ];

    /**
     * Get the adoption case this report belongs to.
     */
    public function adoptionCase()
    {
        return $this->belongsTo(AdoptionCase::class);
    }

    /**
     * Get the user who submitted this report.
     */
    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }
}
